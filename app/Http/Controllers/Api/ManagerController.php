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
                'items' => $items->map(function (VnbPlanItem $item) use ($employee) {
                    // Determine approval type for this item
                    $approvalType = 'all_approved';
                    if (!$item->approved_functional_by) {
                        $approvalType = 'functional';
                    } elseif (!$item->approved_operational_by && $employee->manager_operational_id !== null) {
                        $approvalType = 'operational';
                    }

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
                        'approval_type' => $approvalType, // 'functional', 'operational', or 'all_approved'
                        'approved_functional_by' => $item->approved_functional_by,
                        'approved_functional_at' => optional($item->approved_functional_at)->toDateTimeString(),
                        'approved_operational_by' => $item->approved_operational_by,
                        'approved_operational_at' => optional($item->approved_operational_at)->toDateTimeString(),
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

        // Get requests by ownership (manager is owner of stage)
        $ownershipData = $this->getMyApprovalRequestsByOwnership();
        $myApprovals = $ownershipData['my_approvals'];

        // Get monitoring requests (manager is not owner but can see)
        $monitoringRequests = $this->getMyMonitoringRequests();

        // Combine for total stats
        $allRequests = array_merge($myApprovals, $monitoringRequests);
        $planningCount = count(array_filter($myApprovals, fn($r) => $r['type'] === 'planning'));
        $activityCount = count(array_filter($myApprovals, fn($r) => $r['type'] === 'activity'));

        return response()->json([
            'success' => true,
            'summary' => [
                'planning_count' => $planningCount,
                'activity_count' => $activityCount,
                'total_approval_needed' => count($myApprovals),
                'total_monitoring' => count($monitoringRequests),
            ],
            'data' => [
                'my_approvals' => array_values($myApprovals),
                'monitoring' => array_values($monitoringRequests),
            ],
        ]);
    }

    /**
     * Manager Portal: lightweight count for sidebar badge
     */
    public function myApprovalSummary(): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $manager = $this->resolveCurrentManager();
        if (!$manager && !auth()->user()?->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Data manager untuk akun ini tidak ditemukan.',
            ], 404);
        }

        // Get requests filtered by role-based ownership
        $requestData = $this->getMyApprovalRequestsByOwnership();
        $planningCount = $requestData['planning_count'];
        $activityCount = $requestData['activity_count'];

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

        // Check authorization: ONLY functional manager can approve planning
        $manager = $this->resolveCurrentManager();
        $isAuthorized = $manager && $plan->employee->manager_functional_id == $manager->id;

        abort_unless($isAuthorized, 403, 'Hanya manager fungsional yang dapat approve planning tahap ini');
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

    /**
     * Manager: Approve planning item per-row
     * POST /api/manager/plans/{planId}/items/{itemId}/approve
     */
    public function approvePlanningItem(Request $request, int $planId, int $itemId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $plan = VnbPlan::with('employee')->findOrFail($planId);
        $item = VnbPlanItem::findOrFail($itemId);

        // Validate that item belongs to this plan
        abort_unless($item->plan_id == $planId, 422, 'Item tidak ditemukan di planning ini');

        // Determine current approval stage and validate manager owns it
        $stage = $this->getCurrentApprovalStage($plan->employee);
        $manager = $this->resolveCurrentManager();
        
        abort_unless($this->isManagerStageOwner($plan->employee, $stage), 403, 
            'Anda tidak memiliki otorisasi untuk approve tahap ini');

        try {
            DB::beginTransaction();

            // Update item approval status based on current stage
            if ($stage === 'planning') {
                // Functional manager approves planning items
                $item->update([
                    'approved_functional_by' => $manager->id,
                    'approved_functional_at' => now(),
                ]);
            } else {
                // Operational manager approves activity items
                $item->update([
                    'approved_operational_by' => $manager->id,
                    'approved_operational_at' => now(),
                ]);
            }

            // Check if all items are fully approved for current stage
            if ($stage === 'planning') {
                // All items must have functional approval
                $allApproved = VnbPlanItem::where('plan_id', $planId)
                    ->whereNull('approved_functional_by')
                    ->count() === 0;

                if ($allApproved && $plan->status === 'waiting_manager_approval') {
                    $plan->update([
                        'status' => 'approved',
                        'approved_by' => $manager->id,
                        'approved_at' => now(),
                    ]);
                }
            } else {
                // All items must have operational approval
                $allApproved = VnbPlanItem::where('plan_id', $planId)
                    ->whereNull('approved_operational_by')
                    ->count() === 0;

                if ($allApproved && $plan->status === 'waiting_execution_approval') {
                    $plan->update([
                        'status' => 'activity_approved',
                        'approved_by' => $manager->id,
                        'approved_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil di-approve',
                'data' => [
                    'item_id' => $item->id,
                    'activity_title' => $item->activity_title,
                    'stage' => $stage,
                    'approved_by' => $manager->name,
                    'approved_at' => now()->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve aktivitas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manager: Request revision for planning item per-row
     * POST /api/manager/plans/{planId}/items/{itemId}/request-revision
     */
    public function requestRevisionForItem(Request $request, int $planId, int $itemId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $request->validate([
            'revision_notes' => 'required|string|min:3',
        ]);

        $plan = VnbPlan::with('employee')->findOrFail($planId);
        $item = VnbPlanItem::findOrFail($itemId);

        // Validate that item belongs to this plan
        abort_unless($item->plan_id == $planId, 422, 'Item tidak ditemukan di planning ini');

        // Check authorization
        $manager = $this->resolveCurrentManager();
        $isAuthorized = $manager && (
            $plan->employee->manager_functional_id == $manager->id ||
            $plan->employee->manager_operational_id == $manager->id
        );

        abort_unless($isAuthorized, 403, 'Anda bukan manager dari new hire ini');

        try {
            DB::beginTransaction();

            // Get or create new revision
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

            // Create revision detail for this specific item
            VnbPlanRevisionDetail::create([
                'vnb_plan_revision_id' => $revision->id,
                'vnb_plan_item_id' => $item->id,
                'changed_by' => $manager->id,
                'old_values' => json_encode($item->only(['activity_title', 'description', 'implementation_date'])),
            ]);

            // Update plan status
            $plan->update([
                'status' => 'revision_requested',
                'revision_count' => $plan->revision_count + 1,
            ]);

            // Update item revision status
            $item->update([
                'revision_notes' => $request->revision_notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan revisi berhasil dikirim untuk aktivitas ini',
                'data' => [
                    'revision_id' => $revision->id,
                    'revision_number' => $revision->revision_number,
                    'item_id' => $item->id,
                    'activity_title' => $item->activity_title,
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
     * Manager: Batch review planning items (approves and revisions)
     * POST /api/manager/plans/{planId}/batch-review
     */
    public function batchReviewPlanItems(Request $request, int $planId): JsonResponse
    {
        $this->authorizeManagerPortalAccess();

        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.id' => 'required|integer|exists:vnb_plan_items,id',
            'reviews.*.action' => 'required|in:approve,revise',
            'reviews.*.notes' => 'nullable|string',
        ]);

        $plan = VnbPlan::with('employee')->findOrFail($planId);
        
        // Determine stage and validate manager owns it
        $stage = $this->getCurrentApprovalStage($plan->employee);
        $manager = $this->resolveCurrentManager();
        
        abort_unless($this->isManagerStageOwner($plan->employee, $stage), 403, 
            'Anda tidak memiliki otorisasi untuk review tahap ini');

        try {
            DB::beginTransaction();

            $reviews = $request->input('reviews');
            $hasRevisions = false;
            $revisionRecord = null;
            $managerId = $manager->id;

            foreach ($reviews as $review) {
                $item = VnbPlanItem::where('id', $review['id'])->where('plan_id', $planId)->first();
                if (!$item) continue;

                if ($review['action'] === 'approve') {
                    if ($stage === 'planning') {
                        // Functional manager approves planning items
                        $item->approved_functional_by = $managerId;
                        $item->approved_functional_at = now();
                    } else {
                        // Operational manager approves activity items
                        $item->approved_operational_by = $managerId;
                        $item->approved_operational_at = now();
                    }
                    $item->save();
                } elseif ($review['action'] === 'revise') {
                    $hasRevisions = true;

                    // Group all revisions into one revision round
                    if (!$revisionRecord) {
                        $latestRevision = $plan->revisions()->orderByDesc('revision_number')->first();
                        $newRevisionNumber = ($latestRevision?->revision_number ?? 0) + 1;

                        $revisionRecord = VnbPlanRevision::create([
                            'vnb_plan_id' => $plan->id,
                            'revision_number' => $newRevisionNumber,
                            'requested_by' => $managerId,
                            'stage' => $stage,
                            'revision_notes' => 'Grouped batch revision',
                            'status' => 'pending',
                            'requested_at' => now(),
                        ]);
                    }

                    VnbPlanRevisionDetail::create([
                        'vnb_plan_revision_id' => $revisionRecord->id,
                        'vnb_plan_item_id' => $item->id,
                        'changed_by' => $managerId,
                        'old_values' => json_encode($item->only(['activity_title', 'description', 'implementation_date', 'deliverables'])),
                    ]);

                    $item->update([
                        'revision_notes' => $review['notes'],
                    ]);
                }
            }

            // Check if all items are fully approved for current stage
            if ($stage === 'planning') {
                $allApproved = VnbPlanItem::where('plan_id', $planId)
                    ->whereNull('approved_functional_by')
                    ->count() === 0;

                if ($allApproved && in_array($plan->status, ['waiting_manager_approval', 'submitted'])) {
                    $plan->update([
                        'status' => 'approved',
                        'approved_by' => $managerId,
                        'approved_at' => now(),
                    ]);
                }
            } else {
                $allApproved = VnbPlanItem::where('plan_id', $planId)
                    ->whereNull('approved_operational_by')
                    ->count() === 0;

                if ($allApproved && $plan->status === 'waiting_execution_approval') {
                    $plan->update([
                        'status' => 'activity_approved',
                        'approved_by' => $managerId,
                        'approved_at' => now(),
                    ]);
                }
            }

            if ($hasRevisions) {
                $plan->update([
                    'status' => 'revision_requested',
                    'revision_count' => $plan->revision_count + 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review berhasil disimpan secara batch',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan review: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated manager's profile info
     */
    public function getMyProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->hasRole('manager')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya manager yang dapat mengakses profil ini'
            ], 403);
        }

        $manager = Manager::where('user_id', $user->id)->first();
        $employee = $manager ? Employee::find($manager->employee_id) : null;

        if (!$manager || !$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data manager tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'employee_id' => $employee->id,
                'employee_number' => $employee->employee_number,
                'division' => $employee->division?->name,
                'department' => $employee->department?->name,
                'position' => $employee->position?->name,
                'level' => $employee->level,
                'date_joined' => $employee->date_joined?->toDateString(),
                'status' => $user->status,
                'created_at' => $user->created_at?->toDateString(),
            ]
        ]);
    }

    /**
     * Update authenticated manager's profile (name, phone, email)
     */
    public function updateMyProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->hasRole('manager')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya manager yang dapat mengakses fitur ini'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $updateData = [];
        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }
        if (isset($validated['phone'])) {
            $updateData['phone'] = $validated['phone'];
        }
        if (isset($validated['email']) && !empty($validated['email'])) {
            $updateData['email'] = $validated['email'];
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        ]);
    }

    /**
     * ========== APPROVAL WORKFLOW HELPERS ==========
     * Role-based approval per stage implementation
     * Planning stage: owner = manager_functional_id
     * Activity stage: owner = manager_operational_id || manager_functional_id (fallback)
     */

    /**
     * Determine employee manager mode (single vs dual)
     *
     * @return string 'single' | 'dual'
     */
    private function getEmployeeManagerMode(Employee $employee): string
    {
        // If employee has only functional manager OR operational is null/same as functional
        if (!$employee->manager_operational_id || $employee->manager_operational_id === $employee->manager_functional_id) {
            return 'single';
        }
        return 'dual';
    }

    /**
     * Get current approval stage for an employee based on plan status
     *
     * @return string 'planning' | 'activity'
     */
    private function getCurrentApprovalStage(Employee $employee): string
    {
        $latestPlan = VnbPlan::where('employee_id', $employee->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if (!$latestPlan || in_array($latestPlan->status, ['draft', 'waiting_manager_approval', 'rejected'])) {
            return 'planning';
        }

        return 'activity';
    }

    /**
     * Determine which manager is the owner for a specific approval stage
     * Returns manager ID or null if no owner found
     *
     * @param string $stage 'planning' | 'activity'
     * @return int|null
     */
    private function getStageOwnerManagerId(Employee $employee, string $stage): ?int
    {
        if ($stage === 'planning') {
            // Planning stage always owned by functional manager
            return $employee->manager_functional_id;
        }

        if ($stage === 'activity') {
            // Activity stage: operational manager if exists, else fallback to functional
            if ($employee->manager_operational_id && $employee->manager_operational_id !== $employee->manager_functional_id) {
                return $employee->manager_operational_id;
            }
            return $employee->manager_functional_id;
        }

        return null;
    }

    /**
     * Check if current authenticated manager is the owner of approval stage
     *
     * @param Employee $employee
     * @param string $stage 'planning' | 'activity'
     * @return bool
     */
    private function isManagerStageOwner(Employee $employee, string $stage): bool
    {
        $manager = $this->resolveCurrentManager();
        if (!$manager) {
            return auth()->user()?->hasRole('admin') ?? false;
        }

        $ownerManagerId = $this->getStageOwnerManagerId($employee, $stage);
        return $manager->id === $ownerManagerId;
    }

    /**
     * Get approval requests filtered by role/stage ownership
     * Returns only requests where manager is the owner of current stage
     *
     * @return array
     */
    private function getMyApprovalRequestsByOwnership(): array
    {
        $employeeIds = $this->resolveManagerEmployeeIds();
        if ($employeeIds === null) {
            return [];
        }

        $employees = Employee::whereIn('id', $employeeIds)->get();

        $planRequests = [];
        $activityRequests = [];

        foreach ($employees as $employee) {
            $currentStage = $this->getCurrentApprovalStage($employee);
            $isStageOwner = $this->isManagerStageOwner($employee, $currentStage);

            // Planning requests: only if manager is owner and stage is 'planning'
            if ($currentStage === 'planning' && $isStageOwner) {
                $plan = VnbPlan::where('employee_id', $employee->id)
                    ->where('status', 'waiting_manager_approval')
                    ->orderByDesc('submitted_at')
                    ->first();

                if ($plan) {
                    $planRequests[] = [
                        'type' => 'planning',
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'employee_number' => $employee->employee_number,
                        'company' => $employee->company,
                        'reference_id' => $plan->id,
                        'title' => $plan->title ?: 'Planning Approval',
                        'phase' => 'Planning',
                        'submitted_at' => optional($plan->submitted_at)->toDateTimeString(),
                        'stage' => 'planning',
                        'approval_mode' => $this->getEmployeeManagerMode($employee),
                    ];
                }
            }

            // Activity requests: only if manager is owner and stage is 'activity'
            if ($currentStage === 'activity' && $isStageOwner) {
                $items = VnbPlanItem::where('submission_status', 'waiting_approval')
                    ->whereHas('plan', function ($q) use ($employee) {
                        $q->where('employee_id', $employee->id);
                    })
                    ->orderByDesc('submitted_at')
                    ->get();

                foreach ($items as $item) {
                    $activityRequests[] = [
                        'type' => 'activity',
                        'employee_id' => $employee->id,
                        'employee_name' => $item->plan?->employee?->name,
                        'employee_number' => $item->plan?->employee?->employee_number,
                        'company' => $item->plan?->employee?->company,
                        'reference_id' => $item->id,
                        'title' => $item->activity_title,
                        'phase' => 'Fase ' . ($item->plan?->phase_number ?? 1),
                        'submitted_at' => optional($item->submitted_at)->toDateTimeString(),
                        'stage' => 'activity',
                        'approval_mode' => $this->getEmployeeManagerMode($item->plan?->employee),
                    ];
                }
            }
        }

        return [
            'my_approvals' => array_merge($planRequests, $activityRequests),
            'planning_count' => count($planRequests),
            'activity_count' => count($activityRequests),
        ];
    }

    /**
     * Get monitoring requests (for non-owners to see)
     * Returns requests where manager is NOT the owner but can see
     *
     * @return array
     */
    private function getMyMonitoringRequests(): array
    {
        $employeeIds = $this->resolveManagerEmployeeIds();
        if ($employeeIds === null) {
            return [];
        }

        $employees = Employee::whereIn('id', $employeeIds)->get();
        $monitoringRequests = [];

        foreach ($employees as $employee) {
            $currentStage = $this->getCurrentApprovalStage($employee);
            $isStageOwner = $this->isManagerStageOwner($employee, $currentStage);

            // If manager is NOT the owner, add to monitoring
            if (!$isStageOwner) {
                // Get current request being processed
                if ($currentStage === 'planning') {
                    $plan = VnbPlan::where('employee_id', $employee->id)
                        ->where('status', 'waiting_manager_approval')
                        ->orderByDesc('submitted_at')
                        ->first();

                    if ($plan) {
                        $monitoringRequests[] = [
                            'type' => 'planning',
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'employee_number' => $employee->employee_number,
                            'company' => $employee->company,
                            'reference_id' => $plan->id,
                            'title' => $plan->title ?: 'Planning Approval',
                            'phase' => 'Planning',
                            'submitted_at' => optional($plan->submitted_at)->toDateTimeString(),
                            'stage' => 'planning',
                            'approval_mode' => $this->getEmployeeManagerMode($employee),
                            'owner_type' => 'functional',
                        ];
                    }
                } else if ($currentStage === 'activity') {
                    $items = VnbPlanItem::where('submission_status', 'waiting_approval')
                        ->whereHas('plan', function ($q) use ($employee) {
                            $q->where('employee_id', $employee->id);
                        })
                        ->orderByDesc('submitted_at')
                        ->get();

                    $ownerType = $employee->manager_operational_id && $employee->manager_operational_id !== $employee->manager_functional_id
                        ? 'operational'
                        : 'functional';

                    foreach ($items as $item) {
                        $monitoringRequests[] = [
                            'type' => 'activity',
                            'employee_id' => $employee->id,
                            'employee_name' => $item->plan?->employee?->name,
                            'employee_number' => $item->plan?->employee?->employee_number,
                            'company' => $item->plan?->employee?->company,
                            'reference_id' => $item->id,
                            'title' => $item->activity_title,
                            'phase' => 'Fase ' . ($item->plan?->phase_number ?? 1),
                            'submitted_at' => optional($item->submitted_at)->toDateTimeString(),
                            'stage' => 'activity',
                            'approval_mode' => $this->getEmployeeManagerMode($item->plan?->employee),
                            'owner_type' => $ownerType,
                        ];
                    }
                }
            }
        }

        return $monitoringRequests;
    }
}
