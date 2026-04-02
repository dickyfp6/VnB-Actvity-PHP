<?php

namespace App\Http\Controllers\Api;

use App\Models\Manager;
use App\Models\Employee;
use App\Models\User;
use App\Models\VnbPlanItem;
use App\Models\VnbPlan;
use App\Models\VnbPlanRevision;
use App\Models\VnbPlanRevisionDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManagerController extends Controller
{
    /**
     * UC003: List managers with new hire counts
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeManagerAccess();

        $query = Manager::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $managers = $query->orderBy('email')->get();

        $employeeAssignments = Employee::query()
            ->where(function ($q) use ($managers) {
                $q->whereIn('manager_functional_id', $managers->pluck('id'))
                  ->orWhereIn('manager_operational_id', $managers->pluck('id'));
            })
            ->get(['id', 'vnb_status', 'manager_functional_id', 'manager_operational_id']);

        $employeeProgressMap = VnbPlanItem::query()
            ->join('vnb_plans', 'vnb_plans.id', '=', 'vnb_plan_items.plan_id')
            ->selectRaw('vnb_plans.employee_id as employee_id, AVG(vnb_plan_items.completion_percentage) as avg_progress')
            ->groupBy('vnb_plans.employee_id')
            ->pluck('avg_progress', 'employee_id');

        $rows = $managers->map(function (Manager $manager) use ($employeeAssignments, $employeeProgressMap) {
            $functionalEmployees = $employeeAssignments
                ->where('manager_functional_id', $manager->id)
                ->pluck('id');

            $operationalEmployees = $employeeAssignments
                ->where('manager_operational_id', $manager->id)
                ->pluck('id');

            $allEmployeeIds = $functionalEmployees->merge($operationalEmployees)->unique()->values();

            $progressValues = $allEmployeeIds->map(function ($employeeId) use ($employeeAssignments, $employeeProgressMap) {
                $calculated = $employeeProgressMap->get($employeeId);
                if ($calculated !== null) {
                    return (float) $calculated;
                }

                $status = $employeeAssignments->firstWhere('id', $employeeId)?->vnb_status;
                return match ($status) {
                    'completed' => 100,
                    default => 0,
                };
            });

            $avgProgress = $progressValues->count() ? round($progressValues->avg(), 1) : 0;

            return [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'employee_number' => $manager->employee_number,
                'company' => $manager->company,
                'division' => $manager->division,
                'status' => $manager->status,
                'new_hire_count' => $allEmployeeIds->count(),
                'functional_new_hires_count' => $functionalEmployees->count(),
                'operational_new_hires_count' => $operationalEmployees->count(),
                'progress_new_hire' => $avgProgress,
                'has_account' => (bool) $manager->user_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * UC003: Manager detail
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeManagerAccess();

        $manager = Manager::with('user')->findOrFail($id);
        $employees = $this->getEmployeesUnderManager($id);
        $progressMap = $this->buildProgressMap($employees);

        $functionalCount = $employees->where('manager_functional_id', $id)->count();
        $operationalCount = $employees->where('manager_operational_id', $id)->count();
        $avgProgress = $progressMap->count() ? round((float) $progressMap->avg(), 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'employee_number' => $manager->employee_number,
                'company' => $manager->company,
                'division' => $manager->division,
                'status' => $manager->status,
                'new_hire_count' => $employees->count(),
                'functional_new_hires_count' => $functionalCount,
                'operational_new_hires_count' => $operationalCount,
                'progress_new_hire' => $avgProgress,
                'has_account' => (bool) $manager->user_id,
                'account_credential_preview' => $this->buildManagerCredentialPreview($manager),
            ],
        ]);
    }

    /**
     * UC003: Reset manager credential
     */
    public function resetCredential(int $id): JsonResponse
    {
        $this->authorizeManagerAccess();

        $manager = Manager::with('user')->findOrFail($id);
        $rawPassword = $this->resetManagerCredentialInternal($manager);

        return response()->json([
            'success' => true,
            'message' => 'Password sementara manager berhasil di-generate ulang.',
            'data' => [
                ...$this->buildManagerCredentialPreview($manager->fresh('user')),
                'temporary_password' => $rawPassword,
            ],
        ]);
    }

    /**
     * UC003 Scenario A: Add Manager
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeManagerAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:managers,email|unique:users,email',
            'employee_number' => 'required|string|max:50|unique:managers,employee_number',
            'company' => ['nullable', 'string', 'max:100', Rule::exists('master_companies', 'name')],
            'division' => ['nullable', 'string', 'max:100', Rule::exists('master_divisions', 'name')],
        ]);
        $rawPassword = null;

        $manager = DB::transaction(function () use ($validated, &$rawPassword) {
            $rawPassword = $this->buildDefaultPasswordFromManager(
                (string) $validated['name'],
                (string) $validated['employee_number']
            );

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($rawPassword),
                'temp_password_encrypted' => Crypt::encryptString($rawPassword),
                'temp_password_generated_at' => now(),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $user->assignRole('manager');

            return Manager::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'employee_number' => trim((string) $validated['employee_number']),
                'company' => $validated['company'] ?? null,
                'division' => $validated['division'] ?? null,
                'status' => 'active',
                'user_id' => $user->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Manager berhasil ditambahkan dan akun manager berhasil dibuat',
            'data' => [
                ...$manager->toArray(),
                'account_credential' => [
                    'username' => $manager->email,
                    'username_email' => $manager->email,
                    'username_nip' => $manager->employee_number,
                    'email' => $manager->email,
                    'role' => 'manager',
                    'temporary_password' => $rawPassword,
                ],
            ],
        ], 201);
    }

    /**
     * UC003 Scenario B: Update Manager
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->authorizeManagerAccess();

        $manager = Manager::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|email|unique:managers,email,' . $id,
            'employee_number' => 'sometimes|required|string|max:50|unique:managers,employee_number,' . $id,
            'company' => ['nullable', 'string', 'max:100', Rule::exists('master_companies', 'name')],
            'division' => ['nullable', 'string', 'max:100', Rule::exists('master_divisions', 'name')],
            'status' => 'sometimes|in:active,inactive',
        ]);

        DB::transaction(function () use ($manager, $validated) {
            $emailChanged = array_key_exists('email', $validated)
                && Str::lower(trim((string) $validated['email'])) !== Str::lower(trim((string) $manager->email));

            if ($emailChanged) {
                $emailConflict = User::query()
                    ->whereRaw('LOWER(email) = ?', [Str::lower((string) $validated['email'])])
                    ->when($manager->user_id, function ($query) use ($manager) {
                        $query->where('id', '!=', $manager->user_id);
                    })
                    ->exists();

                if ($emailConflict) {
                    throw ValidationException::withMessages([
                        'email' => ['Email sudah digunakan akun user lain.'],
                    ]);
                }
            }

            $managerData = [
                ...$validated,
            ];

            if (array_key_exists('employee_number', $managerData)) {
                $managerData['employee_number'] = trim((string) $managerData['employee_number']);
            }

            $manager->update($managerData);

            if ($manager->user_id) {
                $user = User::find($manager->user_id);
                if ($user) {
                    $user->update([
                        'name' => $manager->name,
                        'email' => $manager->email,
                        'status' => $manager->status,
                    ]);

                    if (!$user->hasRole('manager')) {
                        $user->syncRoles(['manager']);
                    }
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Data Manager berhasil diperbarui', 'data' => $manager]);
    }

    /**
     * UC003 Scenario C: Delete Manager
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeManagerAccess();

        $manager = Manager::findOrFail($id);

        // Check if manager is assigned to any New Hire
        $inUse = Employee::where('manager_functional_id', $id)
            ->orWhere('manager_operational_id', $id)
            ->exists();

        if ($inUse) {
            return response()->json([
                'success' => false,
                'message' => 'Manager tidak dapat dihapus karena masih terdaftar sebagai Manager New Hire.',
            ], 422);
        }

        $manager->delete();

        return response()->json(['success' => true, 'message' => 'Data Manager berhasil dihapus']);
    }

    /**
     * UC003 Scenario D: List New Hires under a Manager
     */
    public function newHires(int $id): JsonResponse
    {
        $this->authorizeManagerAccess();

        $manager = Manager::findOrFail($id);

        $employees = $this->getEmployeesUnderManager($id);
        $progressMap = $this->buildProgressMap($employees);
        $managerNameMap = $this->buildManagerNameMap($employees);
        $latestPlanMap = $this->buildLatestPlanMap($employees);

        $rows = $employees->map(fn (Employee $employee) => $this->formatNewHireRow(
            $employee,
            $progressMap,
            $managerNameMap,
            $latestPlanMap
        ));

        return response()->json([
            'success' => true,
            'manager' => $manager,
            'data'    => $rows,
        ]);
    }

    /**
     * Manager Portal: List assigned new hires (active/history)
     */
    public function myNewHires(Request $request): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $manager = $this->resolveCurrentManager();
        if (!$manager && !auth()->user()?->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Data manager untuk akun ini tidak ditemukan.',
            ], 404);
        }

        $query = Employee::query()->with(['division', 'department', 'position', 'managerFunctional', 'managerOperational']);
        if ($manager) {
            $query->where(function ($q) use ($manager) {
                $q->where('manager_functional_id', $manager->id)
                    ->orWhere('manager_operational_id', $manager->id);
            });
        }

        $lifecycle = (string) $request->input('lifecycle', 'active');
        if ($lifecycle === 'history') {
            $query->whereIn('employment_state', ['resigned', 'terminated', 'graduated']);
        } elseif (in_array($lifecycle, ['active', 'resigned', 'terminated', 'graduated'], true)) {
            $query->where('employment_state', $lifecycle);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->get();
        $progressMap = $this->buildProgressMap($employees);
        $latestPlanMap = $this->buildLatestPlanMap($employees);

        $rows = $employees->map(function (Employee $employee) use ($progressMap, $latestPlanMap) {
            $progress = $progressMap->get($employee->id);
            if ($progress === null) {
                $progress = match ($employee->vnb_status) {
                    'completed' => 100,
                    default => 0,
                };
            }

            return [
                'id' => $employee->id,
                'employee_number' => $employee->employee_number,
                'name' => $employee->name,
                'date_joined' => $employee->date_joined?->format('Y-m-d'),
                'induction_date' => $employee->induction_date?->format('Y-m-d'),
                'email' => $employee->email,
                'whatsapp' => $employee->whatsapp,
                'vnb_period_start' => $employee->vnb_period_start?->format('Y-m-d'),
                'vnb_period_end' => $employee->vnb_period_end?->format('Y-m-d'),
                'company' => $employee->company,
                'division' => $employee->division?->name,
                'department' => $employee->department?->name,
                'position' => $employee->position?->name,
                'placement' => $employee->placement,
                'level' => $employee->level,
                'employee_status' => $employee->employee_status,
                'career_stage' => $employee->level,
                'manager_functional' => $employee->managerFunctional?->name,
                'manager_operational' => $employee->managerOperational?->name,
                'phase' => $this->deriveEmployeePhaseLabel($employee, $latestPlanMap->get($employee->id)),
                'progress' => round((float) $progress, 1),
                'employment_state' => $employee->employment_state ?? 'active',
                'vnb_status' => $employee->vnb_status,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Manager Portal: New hire detail for review/approval
     */
    public function myNewHireDetail(int $employeeId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $manager = $this->resolveCurrentManager();
        $isAdmin = auth()->user()?->hasRole('admin');

        $employeeQuery = Employee::query()->with(['division', 'department', 'position', 'managerFunctional', 'managerOperational']);
        if (!$isAdmin) {
            if (!$manager) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data manager untuk akun ini tidak ditemukan.',
                ], 404);
            }

            $employeeQuery->where(function ($q) use ($manager) {
                $q->where('manager_functional_id', $manager->id)
                    ->orWhere('manager_operational_id', $manager->id);
            });
        }

        $employee = $employeeQuery->findOrFail($employeeId);

        $plan = VnbPlan::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->with('items')
            ->first();

        $items = $plan?->items ?? collect();
        $progress = $items->count() ? round((float) $items->avg('completion_percentage'), 1) : 0;
        $activityWaitingCount = $items->where('submission_status', 'waiting_approval')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'employee_number' => $employee->employee_number,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'company' => $employee->company,
                    'division' => $employee->division?->name,
                    'department' => $employee->department?->name,
                    'position' => $employee->position?->name,
                    'placement' => $employee->placement,
                    'level' => $employee->level,
                    'employee_status' => $employee->employee_status,
                    'manager_functional' => $employee->managerFunctional?->name,
                    'manager_operational' => $employee->managerOperational?->name,
                ],
                'plan' => $plan ? [
                    'id' => $plan->id,
                    'status' => $plan->status,
                    'phase_number' => $plan->phase_number,
                    'title' => $plan->title,
                    'description' => $plan->description,
                    'submitted_at' => optional($plan->submitted_at)->toDateTimeString(),
                    'rejection_reason' => $plan->rejection_reason,
                    'discussion_notes' => $plan->discussion_notes,
                ] : null,
                'is_planning_phase' => $this->deriveEmployeePhaseLabel($employee, $plan) === 'Planning',
                'phase' => $this->deriveEmployeePhaseLabel($employee, $plan),
                'progress' => $progress,
                'approval_requests' => [
                    'planning_waiting' => (bool) ($plan && $plan->status === 'waiting_manager_approval'),
                    'activity_waiting_count' => $activityWaitingCount,
                ],
                'items' => $items->map(function (VnbPlanItem $item) {
                    return [
                        'id' => $item->id,
                        'activity_title' => $item->activity_title,
                        'description' => $item->description,
                        'deliverables' => $item->deliverables,
                        'implementation_date' => optional($item->implementation_date)->toDateString(),
                        'behavior_metrics' => $item->behavior_metrics,
                        'activity_description' => $item->activity_description,
                        'activity_date' => optional($item->activity_date)->toDateString(),
                        'submission_status' => $item->submission_status,
                        'revision_notes' => $item->revision_notes,
                        'completion_percentage' => (int) $item->completion_percentage,
                    ];
                })->values(),
            ],
        ]);
    }

    public function myNewHirePlanningHistory(int $employeeId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $manager = $this->resolveCurrentManager();
        $isAdmin = auth()->user()?->hasRole('admin');

        $employeeQuery = Employee::query();
        if (!$isAdmin) {
            if (!$manager) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data manager untuk akun ini tidak ditemukan.',
                ], 404);
            }

            $employeeQuery->where(function ($q) use ($manager) {
                $q->where('manager_functional_id', $manager->id)
                    ->orWhere('manager_operational_id', $manager->id);
            });
        }

        $employee = $employeeQuery->findOrFail($employeeId);
        $plan = VnbPlan::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->with(['items', 'revisions'])
            ->first();

        if (!$plan) {
            return response()->json([
                'success' => true,
                'data' => [
                    'employee' => [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'employee_number' => $employee->employee_number,
                    ],
                    'plan' => null,
                    'approved_items' => [],
                    'revisions' => [],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'employee_number' => $employee->employee_number,
                ],
                'plan' => [
                    'id' => $plan->id,
                    'status' => $plan->status,
                    'phase_number' => $plan->phase_number,
                ],
                'approved_items' => $plan->items->map(function (VnbPlanItem $item) {
                    return [
                        'activity_title' => $item->activity_title,
                        'description' => $item->description,
                        'deliverables' => $item->deliverables,
                        'implementation_date' => optional($item->implementation_date)->toDateString(),
                    ];
                })->values(),
                'revisions' => $plan->revisions->map(function ($revision) {
                    return [
                        'version_number' => $revision->version_number,
                        'status' => $revision->status,
                        'decision' => $revision->decision,
                        'submitted_at' => optional($revision->submitted_at)->toDateTimeString(),
                        'reviewed_at' => optional($revision->reviewed_at)->toDateTimeString(),
                        'review_notes' => $revision->review_notes,
                        'snapshot_items' => $revision->snapshot['items'] ?? [],
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * Manager Portal: requests waiting for manager approval
     */
    public function myApprovalRequests(): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $employeeIds = $this->resolveManagerEmployeeIds();
        if ($employeeIds === null) {
            return response()->json([
                'success' => false,
                'message' => 'Data manager untuk akun ini tidak ditemukan.',
            ], 404);
        }

        $planRequests = VnbPlan::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'waiting_manager_approval')
            ->with('employee')
            ->orderByDesc('submitted_at')
            ->get()
            ->map(function (VnbPlan $plan) {
                return [
                    'type' => 'planning',
                    'employee_id' => $plan->employee_id,
                    'employee_name' => $plan->employee?->name,
                    'employee_number' => $plan->employee?->employee_number,
                    'company' => $plan->employee?->company,
                    'reference_id' => $plan->id,
                    'title' => $plan->title ?: 'Planning Approval',
                    'phase' => 'Planning',
                    'submitted_at' => optional($plan->submitted_at)->toDateTimeString(),
                ];
            });

        $activityRequests = VnbPlanItem::query()
            ->where('submission_status', 'waiting_approval')
            ->whereHas('plan', function ($q) use ($employeeIds) {
                $q->whereIn('employee_id', $employeeIds);
            })
            ->with('plan.employee')
            ->orderByDesc('submitted_at')
            ->get()
            ->map(function (VnbPlanItem $item) {
                return [
                    'type' => 'activity',
                    'employee_id' => $item->plan?->employee_id,
                    'employee_name' => $item->plan?->employee?->name,
                    'employee_number' => $item->plan?->employee?->employee_number,
                    'company' => $item->plan?->employee?->company,
                    'reference_id' => $item->id,
                    'title' => $item->activity_title,
                    'phase' => 'Fase ' . ($item->plan?->phase_number ?? 1),
                    'submitted_at' => optional($item->submitted_at)->toDateTimeString(),
                ];
            });

        $rows = $planRequests
            ->merge($activityRequests)
            ->sortByDesc('submitted_at')
            ->values();

        return response()->json([
            'success' => true,
            'summary' => [
                'planning_count' => $planRequests->count(),
                'activity_count' => $activityRequests->count(),
                'total_count' => $rows->count(),
            ],
            'data' => $rows,
        ]);
    }

    /**
     * Manager Portal: lightweight count for sidebar badge
     */
    public function myApprovalSummary(): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $employeeIds = $this->resolveManagerEmployeeIds();
        if ($employeeIds === null) {
            return response()->json([
                'success' => false,
                'message' => 'Data manager untuk akun ini tidak ditemukan.',
            ], 404);
        }

        $planningCount = VnbPlan::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'waiting_manager_approval')
            ->count();

        $activityCount = VnbPlanItem::query()
            ->where('submission_status', 'waiting_approval')
            ->whereHas('plan', function ($q) use ($employeeIds) {
                $q->whereIn('employee_id', $employeeIds);
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'planning_count' => $planningCount,
                'activity_count' => $activityCount,
                'total_count' => $planningCount + $activityCount,
            ],
        ]);
    }

    private function getEmployeesUnderManager(int $managerId): Collection
    {
        return Employee::query()
            ->where(function ($q) use ($managerId) {
                $q->where('manager_functional_id', $managerId)
                    ->orWhere('manager_operational_id', $managerId);
            })
            ->with(['division'])
            ->get();
    }

    private function buildProgressMap(Collection $employees): Collection
    {
        return VnbPlanItem::query()
            ->join('vnb_plans', 'vnb_plans.id', '=', 'vnb_plan_items.plan_id')
            ->whereIn('vnb_plans.employee_id', $employees->pluck('id'))
            ->selectRaw('vnb_plans.employee_id as employee_id, AVG(vnb_plan_items.completion_percentage) as avg_progress')
            ->groupBy('vnb_plans.employee_id')
            ->pluck('avg_progress', 'employee_id');
    }

    private function buildManagerNameMap(Collection $employees): Collection
    {
        $managerIds = $employees
            ->pluck('manager_functional_id')
            ->merge($employees->pluck('manager_operational_id'))
            ->filter()
            ->unique();

        return Manager::query()
            ->whereIn('id', $managerIds)
            ->get(['id', 'email', 'name'])
            ->mapWithKeys(function (Manager $manager) {
                return [$manager->id => $manager->name ?: $manager->email];
            });
    }

    private function buildLatestPlanMap(Collection $employees): Collection
    {
        return VnbPlan::query()
            ->whereIn('employee_id', $employees->pluck('id'))
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['employee_id', 'phase_number', 'status'])
            ->groupBy('employee_id')
            ->map(fn ($plans) => $plans->first());
    }

    private function formatNewHireRow(
        Employee $employee,
        Collection $progressMap,
        Collection $managerNameMap,
        Collection $latestPlanMap
    ): array {
        $phase = $this->deriveEmployeePhaseLabel($employee, $latestPlanMap->get($employee->id));
        $progress = $progressMap->get($employee->id);

        if ($progress === null) {
            $progress = match ($employee->vnb_status) {
                'completed' => 100,
                default => 0,
            };
        }

        return [
            'id' => $employee->id,
            'new_hire' => $employee->name,
            'company' => $employee->company,
            'division' => $employee->division?->name,
            'manager_functional' => $employee->manager_functional_id ? ($managerNameMap[$employee->manager_functional_id] ?? '-') : '-',
            'manager_operational' => $employee->manager_operational_id ? ($managerNameMap[$employee->manager_operational_id] ?? '-') : '-',
            'career_stage' => $employee->level,
            'phase' => $phase,
            'progress' => round((float) $progress, 1),
        ];
    }

    private function deriveEmployeePhaseLabel(Employee $employee, mixed $latestPlan): string
    {
        if ($employee->vnb_status === 'completed') {
            return 'Selesai';
        }

        if (!$latestPlan) {
            return 'Planning';
        }

        $planStatus = (string) ($latestPlan->status ?? '');
        if (in_array($planStatus, ['draft', 'waiting_manager_approval', 'rejected'], true)) {
            return 'Planning';
        }

        $phaseNumber = (int) ($latestPlan->phase_number ?? 1);
        if ($phaseNumber < 1 || $phaseNumber > 3) {
            $phaseNumber = 1;
        }

        return 'Fase ' . $phaseNumber;
    }

    private function resolveCurrentManager(): ?Manager
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        return Manager::query()
            ->where('user_id', $user->id)
            ->orWhereRaw('LOWER(email) = ?', [Str::lower(trim((string) $user->email))])
            ->first();
    }

    private function resolveManagerEmployeeIds(): ?Collection
    {
        if (auth()->user()?->hasRole('admin')) {
            return Employee::query()->pluck('id');
        }

        $manager = $this->resolveCurrentManager();
        if (!$manager) {
            return null;
        }

        return Employee::query()
            ->where('manager_functional_id', $manager->id)
            ->orWhere('manager_operational_id', $manager->id)
            ->pluck('id');
    }

    private function buildDefaultPasswordFromManager(string $name, string $employeeNumber): string
    {
        $cleanName = trim($name);
        $firstName = Str::of($cleanName)->explode(' ')->first() ?? '';
        $firstName = preg_replace('/[^A-Za-z0-9]/', '', (string) $firstName) ?: 'Manager';

        $nipDigits = preg_replace('/\D+/', '', $employeeNumber) ?: '';
        $suffix = $nipDigits === ''
            ? '00'
            : str_pad(substr($nipDigits, -2), 2, '0', STR_PAD_LEFT);

        $password = $firstName . $suffix;

        // Keep compatibility with login minimum length rule.
        if (strlen($password) < 6) {
            $password = str_pad($password, 6, '0');
        }

        return $password;
    }

    private function resetManagerCredentialInternal(Manager $manager): string
    {
        $rawPassword = $this->buildDefaultPasswordFromManager(
            (string) $manager->name,
            (string) $manager->employee_number
        );

        $user = $manager->user;
        if (!$user && $manager->user_id) {
            $user = User::find($manager->user_id);
        }

        if ($user && ($user->employee_id || $user->hasRole('new_hire'))) {
            throw ValidationException::withMessages([
                'manager' => ['Akun manager terhubung ke akun New Hire. Hubungi admin untuk perbaikan data user manager.'],
            ]);
        }

        if (!$user) {
            $emailMatchedUser = User::query()
                ->whereRaw('LOWER(email) = ?', [Str::lower(trim((string) $manager->email))])
                ->first();

            if ($emailMatchedUser) {
                // Reject if email is linked to a new hire
                if ($emailMatchedUser->employee_id || $emailMatchedUser->hasRole('new_hire')) {
                    throw ValidationException::withMessages([
                        'email' => ['Email manager sudah dipakai akun New Hire. Gunakan email manager yang berbeda.'],
                    ]);
                }

                // Check if the email-matched user has a different manager linked
                $differentManagerLinked = Manager::query()
                    ->where('user_id', $emailMatchedUser->id)
                    ->where('id', '!=', $manager->id)
                    ->exists();
                if ($differentManagerLinked) {
                    throw ValidationException::withMessages([
                        'email' => ['Email sudah dipakai akun manager lain. Gunakan email manager yang berbeda.'],
                    ]);
                }

                // Only reuse the user if they are manager role and either:
                // 1. They have no manager linked yet, OR
                // 2. They are already linked to this manager
                $hasLinkedManager = Manager::query()->where('user_id', $emailMatchedUser->id)->exists();
                if (!$emailMatchedUser->hasRole('manager')) {
                    // If not manager role, only accept if no manager is linked
                    if ($hasLinkedManager) {
                        throw ValidationException::withMessages([
                            'email' => ['Email sudah dipakai. Gunakan email manager yang berbeda.'],
                        ]);
                    }
                }

                $user = $emailMatchedUser;
            }
        }

        if (!$user) {
            $user = User::create([
                'name' => $manager->name,
                'email' => $manager->email,
                'password' => Hash::make($rawPassword),
                'temp_password_encrypted' => Crypt::encryptString($rawPassword),
                'temp_password_generated_at' => now(),
                'status' => $manager->status ?: 'active',
                'email_verified_at' => now(),
            ]);
            $user->assignRole('manager');

            $manager->update([
                'user_id' => $user->id,
            ]);
        }

        $user->update([
            'name' => $manager->name,
            'email' => $manager->email,
            'status' => $manager->status ?: 'active',
            'password' => Hash::make($rawPassword),
            'temp_password_encrypted' => Crypt::encryptString($rawPassword),
            'temp_password_generated_at' => now(),
        ]);

        if (!$user->hasRole('manager')) {
            $user->assignRole('manager');
        }

        if ((int) $manager->user_id !== (int) $user->id) {
            $manager->update([
                'user_id' => $user->id,
            ]);
        }

        return $rawPassword;
    }

    private function buildManagerCredentialPreview(Manager $manager): array
    {
        $tempPassword = null;
        if ($manager->user?->temp_password_encrypted) {
            try {
                $tempPassword = Crypt::decryptString($manager->user->temp_password_encrypted);
            } catch (\Throwable) {
                $tempPassword = null;
            }
        }

        return [
            'username' => $manager->email,
            'username_email' => $manager->email,
            'username_nip' => $manager->employee_number,
            'role' => 'manager',
            'status' => $manager->user?->status,
            'temporary_password' => $tempPassword,
            'temporary_password_generated_at' => optional($manager->user?->temp_password_generated_at)->toDateTimeString(),
        ];
    }

    private function authorizeManagerAccess(): void
    {
        abort_unless(
            auth()->user()?->hasAnyRole(['admin', 'intercomm', 'pcx_manager']),
            403,
            'Anda tidak memiliki akses ke Manage Manager'
        );
    }

    private function authorizeManagerPortalAccess(): void
    {
        abort_unless(
            auth()->user()?->hasAnyRole(['admin', 'manager']),
            403,
            'Anda tidak memiliki akses ke portal manager'
        );
    }

    /**
     * Manager: Request revision untuk planning dengan catatan
     * POST /api/manager/plans/{planId}/request-revision
     */
    public function requestRevision(Request $request, int $planId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $request->validate([
            'revision_notes' => 'required|string|min:3',
        ]);

        $plan = VnbPlan::with('employee', 'items')->findOrFail($planId);

        // Check authorization - user is manager of this new hire
        $manager = $this->resolveCurrentManager();
        $isAuthorized = $manager && (
            $plan->employee->manager_functional_id == $manager->id ||
            $plan->employee->manager_operational_id == $manager->id
        );

        abort_unless($isAuthorized, 403, 'Anda bukan manager dari new hire ini');

        try {
            DB::beginTransaction();

            // Get oder create new revision
            $latestRevision = $plan->revisions()
                ->orderByDesc('revision_number')
                ->first();

            $newRevisionNumber = ($latestRevision?->revision_number ?? 0) + 1;

            // Create revision record
            $revision = VnbPlanRevision::create([
                'vnb_plan_id' => $plan->id,
                'revision_number' => $newRevisionNumber,
                'requested_by' => $manager->id,
                'revision_notes' => $request->revision_notes,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            // Update plan status
            $plan->update([
                'status' => 'revision_requested',
                'revision_notes' => $request->revision_notes,
                'revision_count' => $plan->revision_count + 1,
            ]);

            // Log activity (optional - requires spatie/laravel-activitylog)
            if (function_exists('activity')) {
                activity('revision_requested')
                    ->performedOn($plan)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'revision_number' => $newRevisionNumber,
                        'notes' => $request->revision_notes,
                    ])
                    ->log('Manager requested revision for planning');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan revisi berhasil dikirim ke new hire',
                'data' => [
                    'revision_id' => $revision->id,
                    'revision_number' => $revision->revision_number,
                    'status' => 'pending',
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim permintaan revisi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manager: Approve planning request
     * POST /api/manager/plans/{planId}/approve
     */
    public function approvePlan(Request $request, int $planId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $plan = VnbPlan::with('employee')->findOrFail($planId);

        // Check authorization
        $manager = $this->resolveCurrentManager();
        $isAuthorized = $manager && (
            $plan->employee->manager_functional_id == $manager->id ||
            $plan->employee->manager_operational_id == $manager->id
        );

        abort_unless($isAuthorized, 403, 'Anda bukan manager dari new hire ini');
        abort_unless(
            in_array($plan->status, ['waiting_manager_approval', 'revision_requested']),
            422,
            'Status planning tidak valid untuk approval'
        );

        try {
            DB::beginTransaction();

            $plan->update([
                'status' => 'approved',
                'approved_by' => $manager->id,
                'approved_at' => now(),
            ]);

            // Jika ada pending revision, tandai sebagai not applicable
            $plan->revisions()
                ->where('status', 'pending')
                ->update(['status' => 'applied']);

            // Log activity (optional - requires spatie/laravel-activitylog)
            if (function_exists('activity')) {
                activity('plan_approved')
                    ->performedOn($plan)
                    ->causedBy(auth()->user())
                    ->log('Manager approved planning');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Planning berhasil diapprove',
                'data' => [
                    'plan_id' => $plan->id,
                    'status' => 'approved',
                    'approved_at' => $plan->approved_at,
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve planning: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get revision history & version control details
     * GET /api/manager/plans/{planId}/revisions/history
     */
    public function getRevisionHistory(int $planId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $plan = VnbPlan::with('employee')->findOrFail($planId);

        // Check authorization
        $manager = $this->resolveCurrentManager();
        $isAuthorized = $manager && (
            $plan->employee->manager_functional_id == $manager->id ||
            $plan->employee->manager_operational_id == $manager->id
        );

        abort_unless($isAuthorized, 403, 'Anda tidak memiliki akses');

        $revisions = $plan->revisions()
            ->with(['requestedBy', 'revisionDetails.planItem', 'revisionDetails.changedBy'])
            ->orderByDesc('revision_number')
            ->get()
            ->map(function (VnbPlanRevision $revision) {
                return [
                    'id' => $revision->id,
                    'revision_number' => $revision->revision_number,
                    'status' => $revision->status,
                    'status_label' => $revision->getStatusLabel(),
                    'revision_notes' => $revision->revision_notes,
                    'requested_by' => $revision->requestedBy?->name,
                    'requested_at' => $revision->requested_at?->format('Y-m-d H:i:s'),
                    'submitted_at' => $revision->submitted_at?->format('Y-m-d H:i:s'),
                    'applied_at' => $revision->applied_at?->format('Y-m-d H:i:s'),
                    'activities_changed' => $revision->revisionDetails->count(),
                    'details' => $revision->revisionDetails->map(function ($detail) {
                        $changes = $detail->getChangedFields();
                        return [
                            'activity_id' => $detail->vnb_plan_item_id,
                            'activity_title' => $detail->planItem?->activity_title,
                            'changed_fields' => $changes,
                            'changed_by' => $detail->changedBy?->name,
                            'changed_at' => $detail->created_at?->format('Y-m-d H:i:s'),
                        ];
                    })->all(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'plan_id' => $plan->id,
                'plan_title' => $plan->title,
                'total_revisions' => $revisions->count(),
                'revisions' => $revisions,
            ]
        ]);
    }

    /**
     * Get pending revisions untuk new hire (from manager)
     * GET /api/manager/my-new-hire-revisions
     */
    public function myNewHireRevisions(): JsonResponse
    {
        $user = auth()->user();
        abort_unless($user !== null, 401);

        // Get employee ID for current user
        $employee = Employee::where('user_id', $user->id)->first();
        abort_unless($employee !== null, 404, 'Data employee tidak ditemukan');

        $revisions = VnbPlanRevision::whereHas('plan', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })
        ->where('status', 'pending')
        ->with(['plan', 'requestedBy'])
        ->orderByDesc('requested_at')
        ->get()
        ->map(function (VnbPlanRevision $revision) {
            return [
                'id' => $revision->id,
                'plan_id' => $revision->vnb_plan_id,
                'revision_number' => $revision->revision_number,
                'revision_notes' => $revision->revision_notes,
                'requested_by' => $revision->requestedBy?->name,
                'requested_at' => $revision->requested_at?->format('Y-m-d H:i:s'),
                'plan_title' => $revision->plan?->title,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $revisions,
        ]);
    }

    /**
     * New Hire: Get all pending revisions (view untuk new hire)
     * GET /api/new-hire/pending-revisions
     */
    public function getNewHirePendingRevisions(): JsonResponse
    {
        $user = auth()->user();
        abort_unless($user !== null, 401);

        $employee = Employee::where('user_id', $user->id)->first();
        abort_unless($employee !== null, 404, 'Data employee tidak ditemukan');

        $revisions = VnbPlanRevision::whereHas('plan', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })
        ->whereIn('status', ['pending', 'in_progress'])
        ->with(['plan', 'requestedBy', 'revisionDetails'])
        ->orderByDesc('requested_at')
        ->get()
        ->map(function (VnbPlanRevision $revision) {
            return [
                'id' => $revision->id,
                'plan_id' => $revision->vnb_plan_id,
                'plan_title' => $revision->plan?->title,
                'plan_phase' => $revision->plan?->phase_number,
                'revision_number' => $revision->revision_number,
                'status' => $revision->status,
                'status_label' => $revision->getStatusLabel(),
                'revision_notes' => $revision->revision_notes,
                'requested_by' => $revision->requestedBy?->name,
                'requested_at' => $revision->requested_at?->format('Y-m-d H:i:s'),
                'items_to_revise' => $revision->revisionDetails->count(),
                'details' => $revision->revisionDetails->map(function ($detail) {
                    return [
                        'activity_id' => $detail->vnb_plan_item_id,
                        'activity_title' => $detail->planItem?->activity_title,
                    ];
                })->all(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $revisions,
        ]);
    }
}
