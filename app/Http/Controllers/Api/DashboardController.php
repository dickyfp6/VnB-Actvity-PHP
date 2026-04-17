<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;
use App\Support\ActiveRoleContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        $user = auth()->user();
        $role = ActiveRoleContext::current(request(), $user) ?? 'employee';
        $scope = $this->resolveEmployeeScope($user, $role);

        return response()->json([
            'success'            => true,
            'role'               => $role,
            'stats'              => $this->buildStats($scope),
            'phase_progress'     => $this->buildPhaseProgress($scope),
            'behaviour_progress' => $this->buildBehaviourProgress($scope),
        ]);
    }

    private function resolveEmployeeScope($user, string $role): \Illuminate\Database\Eloquent\Builder
    {
        $query = Employee::query();
        if ($role === 'employee') {
            $query->where('email', $user->email);
        } elseif ($role === 'manager') {
            $manager = Manager::where('email', $user->email)->first();
            if ($manager) {
                $query->where(function ($q) use ($manager) {
                    $q->where('manager_functional_id', $manager->id)
                      ->orWhere('manager_operational_id', $manager->id);
                });
            } else {
                $query->whereRaw('1=0');
            }
        }
        return $query;
    }

    private function buildStats(\Illuminate\Database\Eloquent\Builder $scope): array
    {
        $employees = (clone $scope)->get(['id', 'vnb_status']);
        return [
            'total'       => $employees->count(),
            'not_started' => $employees->where('vnb_status', 'not_started')->count(),
            'active'      => $employees->where('vnb_status', 'active')->count(),
            'completed'   => $employees->where('vnb_status', 'completed')->count(),
            'canceled'    => $employees->where('vnb_status', 'canceled')->count(),
        ];
    }

    private function buildPhaseProgress(\Illuminate\Database\Eloquent\Builder $scope): array
    {
        $employeeIds = (clone $scope)->pluck('id');
        $phases = [];
        for ($phase = 1; $phase <= 3; $phase++) {
            $items = VnbPlanItem::whereHas('plan', function ($q) use ($employeeIds, $phase) {
                $q->whereIn('employee_id', $employeeIds)->where('phase_number', $phase);
            })->get(['submission_status']);
            $total     = $items->count();
            $completed = $items->where('submission_status', 'completed')->count();
            $phases["phase_{$phase}"] = [
                'total'     => $total,
                'completed' => $completed,
                'percent'   => $total > 0 ? round(($completed / $total) * 100) : 0,
            ];
        }
        return $phases;
    }

    private function buildBehaviourProgress(\Illuminate\Database\Eloquent\Builder $scope): array
    {
        $employeeIds = (clone $scope)->pluck('id');
        $result = [];
        foreach (VnbFrameworkItem::$behaviours as $behaviour) {
            $items = VnbPlanItem::whereHas('plan', function ($q) use ($employeeIds) {
                    $q->whereIn('employee_id', $employeeIds);
                })
                ->where('activity_title', 'like', "%{$behaviour}%")
                ->get(['submission_status']);
            $total     = $items->count();
            $completed = $items->where('submission_status', 'completed')->count();
            $result[] = [
                'behaviour' => $behaviour,
                'total'     => $total,
                'completed' => $completed,
                'percent'   => $total > 0 ? round(($completed / $total) * 100) : 0,
            ];
        }
        return $result;
    }
}
