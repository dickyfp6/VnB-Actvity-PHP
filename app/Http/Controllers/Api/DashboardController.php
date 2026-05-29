<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\VnbActivityAssignment;
use App\Models\VnbFrameworkItem;
use App\Models\VnbPlanItem;
use App\Support\ActiveRoleContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        $user = auth()->user();
        $role = ActiveRoleContext::current(request(), $user) ?? 'employee';
        $scope = $this->resolveActivityScope($user, $role);

        return response()->json([
            'success' => true,
            'role' => $role,
            'scope' => [
                'role_label' => $scope['role_label'],
                'scope_label' => $scope['scope_label'],
                'has_data' => $scope['participants']->isNotEmpty(),
                'empty_title' => $scope['empty_title'],
                'empty_note' => $scope['empty_note'],
            ],
            'stats' => $this->buildStats($scope['employees'], $scope['participants'], $scope['items']),
            'seven_values' => $this->buildSevenValueProgress($scope['employees'], $scope['items']),
            'charts' => $this->buildCharts($scope['items']),
        ]);
    }

    private function resolveActivityScope($user, string $role): array
    {
        $employees = Employee::query()
            ->with(['user', 'managerFunctional', 'managerOperational'])
            ->when($role === 'employee', function ($query) use ($user): void {
                $query->where('email', $user->email);
            })
            ->when($role === 'manager', function ($query) use ($user): void {
                $manager = Manager::where('email', $user->email)->first();

                if ($manager) {
                    $query->where(function ($subQuery) use ($manager): void {
                        $subQuery->where('manager_functional_id', $manager->id)
                            ->orWhere('manager_operational_id', $manager->id);
                    });
                    return;
                }

                $query->whereRaw('1=0');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'employee_number', 'vnb_status', 'manager_functional_id', 'manager_operational_id']);

        $participants = $this->filterParticipantEmployees($employees);
        $items = $this->loadActivityItems($participants->pluck('id')->values());

        return [
            'employees' => $employees,
            'participants' => $participants,
            'items' => $items,
            'role_label' => $this->resolveRoleLabel($role),
            'scope_label' => $this->resolveScopeLabel($role),
            'empty_title' => $this->resolveEmptyTitle($role),
            'empty_note' => $this->resolveEmptyNote($role),
        ];
    }

    private function filterParticipantEmployees(Collection $employees): Collection
    {
        if ($employees->isEmpty()) {
            return collect();
        }

        $activeUserIds = VnbActivityAssignment::query()
            ->where('is_active', true)
            ->whereIn('user_id', $employees->pluck('user.id')->filter()->values())
            ->pluck('user_id')
            ->all();

        return $employees->filter(function ($employee) use ($activeUserIds): bool {
            if (!$employee->user) {
                return false;
            }

            return in_array($employee->user->id, $activeUserIds, true)
                && in_array($employee->vnb_status, ['active', 'completed'], true);
        })->values();
    }

    private function loadActivityItems(Collection $employeeIds): Collection
    {
        if ($employeeIds->isEmpty()) {
            return collect();
        }

        return VnbPlanItem::query()
            ->with('frameworkItem')
            ->join('vnb_plans', 'vnb_plans.id', '=', 'vnb_plan_items.plan_id')
            ->whereIn('vnb_plans.employee_id', $employeeIds)
            ->where('vnb_plans.status', 'approved')
            ->select('vnb_plan_items.*')
            ->orderBy('vnb_plan_items.id')
            ->get();
    }

    private function buildStats(Collection $employees, Collection $participants, Collection $items): array
    {
        $completedItems = $items->where('completion_percentage', 100)->count();
        $activeItems = $items->filter(fn ($item) => (int) $item->completion_percentage > 0 && (int) $item->completion_percentage < 100)->count();
        $notStartedItems = $items->where('completion_percentage', 0)->count();
        $overdueItems = $items->filter(function ($item): bool {
            if (!$item->due_date) {
                return false;
            }

            return Carbon::parse($item->due_date)->isPast() && (int) $item->completion_percentage < 100;
        })->count();

        return [
            'employees' => $employees->count(),
            'participants' => $participants->count(),
            'items' => $items->count(),
            'completed_items' => $completedItems,
            'active_items' => $activeItems,
            'not_started_items' => $notStartedItems,
            'overdue_items' => $overdueItems,
            'overall_progress' => $items->isNotEmpty() ? round((float) $items->avg('completion_percentage'), 1) : 0,
            'completion_rate' => $items->isNotEmpty() ? round(($completedItems / $items->count()) * 100, 1) : 0,
        ];
    }

    private function buildSevenValueProgress(Collection $employees, Collection $items): array
    {
        $behaviours = $this->resolveFrameworkBehaviours($employees, $items);

        $rows = $behaviours->map(function (string $behaviour) use ($items): array {
            $matchedItems = $items->filter(function ($item) use ($behaviour): bool {
                $title = (string) ($item->activity_title ?? '');
                $frameworkBehaviour = $item->frameworkItem?->behaviour;

                return $frameworkBehaviour === $behaviour || Str::contains($title, $behaviour);
            });

            $total = $matchedItems->count();
            $completed = $matchedItems->where('completion_percentage', 100)->count();

            return [
                'behaviour' => $behaviour,
                'total' => $total,
                'completed' => $completed,
                'progress' => $total > 0 ? round((float) $matchedItems->avg('completion_percentage'), 1) : 0,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            ];
        })->values();

        return [
            'labels' => $rows->pluck('behaviour')->values(),
            'bars' => $rows->map(fn ($row) => $row['progress'])->values(),
            'rows' => $rows,
        ];
    }

    private function resolveFrameworkBehaviours(Collection $employees, Collection $items): Collection
    {
        $stageCodes = $employees
            ->map(function ($employee): string {
                return method_exists($employee, 'getCareerStageCode') ? (string) $employee->getCareerStageCode() : '';
            })
            ->filter()
            ->unique()
            ->values();

        $behaviours = collect();

        if (
            $stageCodes->isNotEmpty()
            && Schema::hasTable('vnb_framework_stage_configs')
            && Schema::hasTable('vnb_framework_stage_behaviours')
            && Schema::hasTable('vnb_framework_behaviours')
        ) {
            $behaviours = DB::table('vnb_framework_stage_configs')
                ->join('vnb_framework_stage_behaviours', 'vnb_framework_stage_behaviours.stage_config_id', '=', 'vnb_framework_stage_configs.id')
                ->join('vnb_framework_behaviours', 'vnb_framework_behaviours.id', '=', 'vnb_framework_stage_behaviours.behaviour_id')
                ->whereIn('vnb_framework_stage_configs.career_stage', $stageCodes)
                ->orderByRaw('CASE WHEN vnb_framework_behaviours.sort_order IS NULL OR vnb_framework_behaviours.sort_order = 0 THEN 999999 ELSE vnb_framework_behaviours.sort_order END')
                ->orderBy('vnb_framework_behaviours.id')
                ->pluck('vnb_framework_behaviours.name')
                ->filter()
                ->unique()
                ->values();
        }

        if ($behaviours->isEmpty() && Schema::hasTable('vnb_framework_behaviours')) {
            $behaviours = DB::table('vnb_framework_behaviours')
                ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 999999 ELSE sort_order END')
                ->orderBy('id')
                ->pluck('name')
                ->filter()
                ->unique()
                ->values();
        }

        if ($behaviours->isEmpty()) {
            $behaviours = $items
                ->map(fn ($item) => $item->frameworkItem?->behaviour)
                ->filter()
                ->unique()
                ->values();
        }

        if ($behaviours->isEmpty()) {
            $behaviours = collect(VnbFrameworkItem::$behaviours);
        }

        return $behaviours->values();
    }

    private function buildCharts(Collection $items): array
    {
        $statusBuckets = [
            'Selesai' => 0,
            'Berjalan' => 0,
            'Belum Mulai' => 0,
            'Terlambat' => 0,
        ];

        foreach ($items as $item) {
            if ((int) $item->completion_percentage >= 100) {
                $statusBuckets['Selesai']++;
                continue;
            }

            if ($item->due_date && Carbon::parse($item->due_date)->isPast()) {
                $statusBuckets['Terlambat']++;
                continue;
            }

            if ((int) $item->completion_percentage > 0) {
                $statusBuckets['Berjalan']++;
                continue;
            }

            $statusBuckets['Belum Mulai']++;
        }

        return [
            'status_breakdown' => [
                'labels' => array_keys($statusBuckets),
                'data' => array_values($statusBuckets),
            ],
        ];
    }

    private function resolveRoleLabel(string $role): string
    {
        return match ($role) {
            'manager' => 'Manager',
            'intercomm' => 'Intercomm',
            'pcx_manager' => 'PCX Manager',
            'direktur_utama' => 'Direktur',
            default => 'Employee',
        };
    }

    private function resolveScopeLabel(string $role): string
    {
        return match ($role) {
            'employee' => 'Progress pribadi',
            'manager' => 'Progress bawahan langsung',
            'intercomm', 'pcx_manager', 'direktur_utama' => 'Progress seluruh employee',
            default => 'Progress VnB Activity',
        };
    }

    private function resolveEmptyTitle(string $role): string
    {
        return match ($role) {
            'employee' => 'Belum ada data VnB Activity kamu',
            'manager' => 'Belum ada employee bawahan yang menjadi partisipan',
            default => 'Belum ada data VnB Activity',
        };
    }

    private function resolveEmptyNote(string $role): string
    {
        return match ($role) {
            'employee' => 'Dashboard akan menampilkan progress 7 value setelah kamu menjadi partisipan VnB Activity.',
            'manager' => 'Kalau belum ada employee bawahan yang sedang menjadi partisipan VnB Activity, chart belum bisa ditampilkan.',
            default => 'Dashboard akan menampilkan agregasi 7 value untuk employee yang memiliki data VnB Activity aktif.',
        };
    }
}
