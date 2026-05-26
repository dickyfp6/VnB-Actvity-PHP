<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\VnbCancellation;
use App\Models\VnbFrameworkItem;
use App\Models\Manager;
use App\Models\VnbActivityAssignment;
use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbPeriod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VnbActivityController extends Controller
{
    /**
     * UC006: List activities for the current Employee (active phase only)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data Employee tidak ditemukan'], 404);
        }

        $items = VnbPlanItem::whereHas('plan', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id)
              ->where('status', 'approved');
        })
        ->with(['plan.employee.position', 'evidences'])
        ->get()
        ->map(function (VnbPlanItem $item): array {
            return $this->formatActivityItem($item);
        });

        $periods = VnbPeriod::where('employee_id', $employee->id)->get(['phase_number', 'start_date', 'end_date', 'status']);

        return response()->json([
            'success' => true,
            'employee' => $employee->only('id', 'name', 'vnb_period_start', 'vnb_period_end', 'level'),
            'periods' => $periods,
            'data' => $items,
        ]);
    }

    /**
     * UC006: Submit / save activity report by Employee
     */
    public function submit(Request $request, int $planItemId): JsonResponse
    {
        $item = VnbPlanItem::findOrFail($planItemId);

        if (!in_array($item->submission_status, ['draft', 'revision_required'])) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas sudah disubmit dan tidak dapat diubah.',
            ], 422);
        }

        $validated = $request->validate([
            'activity_description' => 'required|string',
            'activity_date' => 'required|string',
        ]);

        $descriptionParts = collect(explode("\n---\n", $validated['activity_description']))
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '');
        $dateParts = collect(explode("\n---\n", $validated['activity_date']))
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '');

        if ($descriptionParts->isEmpty() || $dateParts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom implementasi harus diisi sebelum dikirim.',
            ], 422);
        }

        if ($descriptionParts->contains('-') || $dateParts->contains('-')) {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom implementasi harus diisi sebelum dikirim.',
            ], 422);
        }

        if (!$item->evidences()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 1 bukti implementasi harus diunggah sebelum submit.',
            ], 422);
        }

        $item->update([
            'activity_description' => $validated['activity_description'],
            'activity_date' => $validated['activity_date'],
            'submission_status' => 'waiting_approval',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil dikirim untuk review manager.',
            'data' => $this->formatActivityItem($item->fresh()),
        ]);
    }

    /**
     * UC006 Scenario B: Save draft (not submit yet)
     */
    public function saveDraft(Request $request, int $planItemId): JsonResponse
    {
        $item = VnbPlanItem::findOrFail($planItemId);

        if (!in_array($item->submission_status, ['draft', 'revision_required'])) {
            return response()->json(['success' => false, 'message' => 'Status tidak memungkinkan perubahan.'], 422);
        }

        $validated = $request->validate([
            'activity_description' => 'nullable|string',
            'activity_date' => 'nullable|string',
        ]);

        $item->update($validated);

        return response()->json(['success' => true, 'message' => 'Draft tersimpan', 'data' => $this->formatActivityItem($item->fresh())]);
    }

    /**
     * UC007: List activities waiting for Manager review
     */
    public function pendingReview(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Find manager record linked to this user
        $manager = Manager::where('email', $user->email)->first();

        if (!$manager) {
            // Fallback: show all pending if admin/intercomm
            $items = VnbPlanItem::whereIn('submission_status', ['waiting_approval', 'submitted'])
                ->with(['plan.employee.position'])
                ->get()
                ->map(fn(VnbPlanItem $item): array => $this->formatActivityItem($item, withEmployee: true));
        } else {
            // Only Employees assigned to this manager
            $employeeIds = Employee::where('manager_functional_id', $manager->id)
                ->orWhere('manager_operational_id', $manager->id)
                ->pluck('id');

            $items = VnbPlanItem::whereIn('submission_status', ['waiting_approval', 'submitted'])
                ->whereHas('plan', fn($q) => $q->whereIn('employee_id', $employeeIds))
                ->with(['plan.employee.position'])
                ->get()
                ->map(fn(VnbPlanItem $item): array => $this->formatActivityItem($item, withEmployee: true));
        }

        return response()->json(['success' => true, 'data' => $items]);
    }

    /**
     * UC007 Scenario Normal: Approve activity
     */
    public function approve(Request $request, int $planItemId): JsonResponse
    {
        $item = VnbPlanItem::findOrFail($planItemId);

        if (!in_array($item->submission_status, ['waiting_approval', 'submitted'], true)) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak dalam status waiting for approval.'], 422);
        }

        $user = Auth::user();
        $manager = Manager::where('email', $user->email)->first();
        $employee = $item->plan->employee;

        // Determine which manager role this user plays
        $isFunctional   = $manager && $employee->manager_functional_id == $manager->id;
        $isOperational  = $manager && $employee->manager_operational_id == $manager->id;

        if ($isFunctional && !$item->approved_functional_by) {
            $item->approved_functional_by = $user->id;
            $item->approved_functional_at = now();
        }

        if ($isOperational && !$item->approved_operational_by) {
            $item->approved_operational_by = $user->id;
            $item->approved_operational_at = now();
        }

        // Check if all required managers have approved
        $requiresOperational = !is_null($employee->manager_operational_id);
        $functionalDone  = !is_null($item->approved_functional_by);
        $operationalDone = !$requiresOperational || !is_null($item->approved_operational_by);

        if ($functionalDone && $operationalDone) {
            $item->submission_status = 'completed';
            $item->status = 'completed';
            $item->completion_percentage = 100;
        }

        $item->save();

        return response()->json([
            'success' => true,
            'message' => $item->submission_status === 'completed'
                ? 'Aktivitas telah disetujui dan dinyatakan Completed.'
                : 'Approval Anda berhasil disimpan. Menunggu approval manager lainnya.',
            'data' => $this->formatActivityItem($item->fresh()),
        ]);
    }

    /**
     * UC007 Scenario B: Request revision
     */
    public function requestRevision(Request $request, int $planItemId): JsonResponse
    {
        $item = VnbPlanItem::findOrFail($planItemId);

        $validated = $request->validate([
            'revision_notes' => 'required|string',
        ]);

        $item->update([
            'submission_status' => 'revision_required',
            'revision_notes'    => $validated['revision_notes'],
            // Reset approvals
            'approved_functional_by'  => null,
            'approved_functional_at'  => null,
            'approved_operational_by' => null,
            'approved_operational_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan revisi berhasil dikirim ke Employee.',
            'data'    => $this->formatActivityItem($item->fresh()),
        ]);
    }

    // ==================== VNB PARTICIPANTS MANAGEMENT ====================
    
    /**
     * Get list of employees with VnB access (participants)
     * Managed by PCX/Intercomm
     */
    public function getParticipants(Request $request): JsonResponse
    {
        $user = auth()->user();
        abort_unless($user && ($user->hasRole('pcx_manager') || $user->hasRole('intercomm')), 403, 'Anda tidak memiliki akses ke daftar participants VnB.');

        $query = Employee::query()
            ->whereIn('vnb_status', ['active', 'completed', 'canceled']);

        if ($request->filled('manager_id')) {
            $managerId = (int) $request->manager_id;
            $query->where(function ($q) use ($managerId) {
                $q->where('manager_functional_id', $managerId)
                  ->orWhere('manager_operational_id', $managerId);
            });
        }

        if ($request->filled('manager_name')) {
            $managerName = $request->manager_name;
            $query->where(function ($q) use ($managerName) {
                $q->where('manager_functional', $managerName)
                  ->orWhere('manager_operational', $managerName);
            });
        }

        $employees = $query->with([
                'division',
                'department',
                'position',
                'managerFunctional',
                'managerOperational',
                'user',
            ])
            ->get();

        $employeeIds = $employees->pluck('id')->values();

        if ($employeeIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $assignmentsByUserId = VnbActivityAssignment::query()
            ->whereIn('user_id', $employees->pluck('user.id')->filter()->values())
            ->with('user.employee')
            ->orderByDesc('assigned_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($assignments) => $assignments->first());

        $progressMap = VnbPlanItem::query()
            ->join('vnb_plans', 'vnb_plans.id', '=', 'vnb_plan_items.plan_id')
            ->whereIn('vnb_plans.employee_id', $employeeIds)
            ->selectRaw('vnb_plans.employee_id as employee_id, AVG(vnb_plan_items.completion_percentage) as avg_progress')
            ->groupBy('vnb_plans.employee_id')
            ->pluck('avg_progress', 'employee_id');

        $latestPlanMap = VnbPlan::query()
            ->whereIn('employee_id', $employeeIds)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['employee_id', 'phase_number', 'status'])
            ->groupBy('employee_id')
            ->map(fn ($plans) => $plans->first());

        $rows = $employees
            ->map(function (Employee $employee) use ($assignmentsByUserId, $progressMap, $latestPlanMap) {
                $assignment = $employee->user ? $assignmentsByUserId->get($employee->user->id) : null;
                $latestPlan = $latestPlanMap->get($employee->id);
                $periodStart = $assignment?->induction_date;
                $periodEnd = $periodStart
                    ? Carbon::parse($periodStart)->copy()->addYear()->subDay()
                    : null;

                $progress = $progressMap->get($employee->id);
                if ($progress === null) {
                    $progress = match ($employee->vnb_status) {
                        'completed' => 100,
                        default => 0,
                    };
                }

                return [
                    'assignment_id' => $assignment?->id,
                    'employee_id' => $employee->id,
                    'employee_number' => $employee->employee_number,
                    'name' => $employee->name,
                    'company' => $employee->company,
                    'division_id' => $employee->division_id,
                    'division' => $employee->division?->name ?? '-',
                    'department_id' => $employee->department_id,
                    'department' => $employee->department?->name ?? '-',
                    'career_stage' => $employee->getCareerStage() ?? '-',
                    'phase' => $this->deriveParticipantPhaseLabel($employee, $latestPlan),
                    'progress' => round((float) $progress, 1),
                    'manager_functional_id' => $employee->manager_functional_id,
                    'manager_functional' => $employee->managerFunctional?->name ?? '-',
                    'manager_functional_label' => $this->resolveParticipantManagerLabel($employee->managerFunctional?->name, $employee->manager_functional_id),
                    'manager_operational_id' => $employee->manager_operational_id,
                    'manager_operational' => $employee->managerOperational?->name ?? '-',
                    'manager_operational_label' => $this->resolveParticipantManagerLabel($employee->managerOperational?->name, $employee->manager_operational_id),
                    'vnb_period_start' => $periodStart ? Carbon::parse($periodStart)->toDateString() : null,
                    'vnb_period_end' => $periodEnd ? $periodEnd->toDateString() : null,
                    'induction_date' => optional($assignment?->induction_date)->toDateString(),
                    'assigned_at' => optional($assignment?->assigned_at)->toDateTimeString(),
                    'vnb_status' => $employee->vnb_status,
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Assign VnB access to an employee (make them a participant)
     */
    public function assignParticipant(Request $request, int $employeeId): JsonResponse
    {
        $validated = $request->validate([
            'induction_date' => ['required', 'date'],
        ]);

        $employee = Employee::query()->with('user')->findOrFail($employeeId);
        $user = $employee->user;
        $periodStart = Carbon::parse($validated['induction_date']);
        $careerStageCode = $employee->getCareerStageCode();

        if (in_array($employee->vnb_status, ['active', 'completed'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee yang sudah aktif atau lulus VnB tidak bisa di-assign ulang.',
            ], 422);
        }

        // Disallow assign when career stage is not configured in VnB framework
        $careerStage = $employee->getCareerStage();
        if (!$careerStage) {
            return response()->json([
                'success' => false,
                'message' => 'Career stage employee belum terkonfigurasi di VnB framework. Silakan atur framework terlebih dahulu.',
            ], 422);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Employee belum memiliki akun user, tidak bisa di-assign ke VnB.',
            ], 422);
        }

        $frameworkItems = VnbFrameworkItem::query()
            ->where('career_stage', $careerStageCode)
            ->get()
            ->groupBy('phase');

        if ($frameworkItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Framework belum tersedia untuk career stage employee ini. Silakan atur framework terlebih dahulu.',
            ], 422);
        }

        $assignment = DB::transaction(function () use ($employee, $user, $validated, $periodStart, $frameworkItems): VnbActivityAssignment {
            $activeAssignment = VnbActivityAssignment::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->latest('id')
                ->first();

            $payload = [
                'assigned_by_user_id' => auth()->id(),
                'is_active' => true,
                'notes' => null,
                'assigned_at' => now(),
                'induction_date' => $validated['induction_date'],
                'revoked_at' => null,
            ];

            if ($activeAssignment) {
                $activeAssignment->update($payload);

                $employee->update(['vnb_status' => 'active']);
                // Persist induction date on employee record so detail view shows it
                $employee->update([
                    'induction_date' => $validated['induction_date'],
                    'vnb_period_start' => $validated['induction_date'],
                ]);
                $this->ensureParticipantVnbPlanSnapshot($employee->fresh(), $periodStart, $frameworkItems);

                return $activeAssignment;
            }

            $assignment = VnbActivityAssignment::create([
                'user_id' => $user->id,
                ...$payload,
            ]);

            // Persist induction date on employee record so detail view shows it
            $employee->update([
                'vnb_status' => 'active',
                'induction_date' => $validated['induction_date'],
                'vnb_period_start' => $validated['induction_date'],
            ]);
            $this->ensureParticipantVnbPlanSnapshot($employee->fresh(), $periodStart, $frameworkItems);

            return $assignment;
        });

        return response()->json([
            'success' => true,
            'message' => 'Employee assigned to VnB Activity',
            'data' => [
                'employee_id' => $employee->id,
                'user_id' => $user->id,
                'assignment_id' => $assignment->id,
                'induction_date' => optional($assignment->induction_date)->toDateString(),
                'assigned_at' => optional($assignment->assigned_at)->toDateTimeString(),
                'vnb_status' => 'active',
            ],
        ]);
    }

    /**
     * Revoke VnB access from an employee
     */
    public function revokeParticipant(Request $request, int $employeeId): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user && ($user->hasRole('pcx_manager') || $user->hasRole('intercomm')), 403, 'Anda tidak memiliki akses untuk membatalkan participant VnB.');

        $validated = $request->validate([
            'reason' => ['nullable', 'in:budaya_kerja,tidak_cocok_vnb,others'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee = Employee::query()->with('user')->findOrFail($employeeId);
        $userAccount = $employee->user;

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak memiliki akun user, tidak bisa di-revoke dari VnB.',
            ], 422);
        }

        $assignment = VnbActivityAssignment::query()
            ->where('user_id', $userAccount->id)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ini tidak memiliki participant VnB yang aktif.',
            ], 422);
        }

        DB::transaction(function () use ($employee, $assignment, $validated, $user) {
            VnbCancellation::create([
                'employee_id' => $employee->id,
                'reason' => $validated['reason'] ?? 'others',
                'notes' => $validated['notes'] ?? 'Dibatalkan dari daftar participant VnB.',
                'canceled_by' => $user->id,
                'canceled_at' => now(),
            ]);

            $assignment->update([
                'is_active' => false,
                'notes' => $validated['notes'] ?? $assignment->notes,
                'revoked_at' => now(),
            ]);

            $employee->update(['vnb_status' => 'canceled']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Employee berhasil di-cancel dari VnB Activity',
            'data' => [
                'employee_id' => $employee->id,
                'assignment_id' => $assignment->id,
                'vnb_status' => 'canceled',
            ],
        ]);
    }

    /**
     * Get participants for a specific activity
     */
    public function getActivityParticipants(int $activityId): JsonResponse
    {
        // TODO: Get activity by ID
        // TODO: Get all employees who have this activity in their plan
        // TODO: Show submission status for each
        
        return response()->json([
            'success' => true,
            'message' => 'Activity participants',
            'data' => [],
        ]);
    }

    // --------------- helpers ---------------

    private function formatActivityItem(VnbPlanItem $item, bool $withEmployee = false): array
    {
        $dueDate = $item->due_date;
        $countdown = $dueDate ? Carbon::today()->diffInDays($dueDate, false) : null;

        $data = [
            'id'                   => $item->id,
            'plan_id'              => $item->plan_id,
            'activity_title'       => $item->activity_title,
            'description'          => $item->description,
            'deliverables'         => $item->deliverables,
            'activity_description' => $item->activity_description,
            'activity_date'        => $item->activity_date,
            'submission_status'    => $item->submission_status,
            'revision_notes'       => $item->revision_notes,
            'submitted_at'         => $item->submitted_at,
            'due_date'             => $dueDate,
            'countdown_days'       => $countdown,
            'is_overdue'           => $countdown !== null && $countdown < 0,
            'approved_functional_by'  => $item->approved_functional_by,
            'approved_functional_at'  => $item->approved_functional_at,
            'approved_operational_by' => $item->approved_operational_by,
            'approved_operational_at' => $item->approved_operational_at,
            'completion_percentage'   => $item->completion_percentage,
            'evidences'            => $item->evidences->map(fn($ev) => [
                'id' => $ev->id,
                'file_name' => $ev->file_name,
                'description' => $ev->description,
            ])->toArray(),
        ];

        if ($withEmployee && $item->relationLoaded('plan')) {
            $employee = $item->plan->employee;
            if ($employee) {
                $data['employee'] = [
                    'id'           => $employee->id,
                    'name'         => $employee->name,
                    'email'        => $employee->email,
                    'company'      => $employee->company,
                    'golongan'     => $employee->position?->name ?? null,
                    'career_stage' => $employee->getCareerStage() ?? null,
                ];
            } else {
                $data['employee'] = null;
            }
        }

        return $data;
    }

    private function deriveParticipantPhaseLabel(Employee $employee, mixed $latestPlan): string
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

    private function resolveParticipantManagerLabel(?string $managerName, ?int $managerId): string
    {
        $name = trim((string) $managerName);
        if ($name !== '') {
            return $name;
        }

        if ($managerId) {
            return 'Manager #' . $managerId;
        }

        return '-';
    }

    private function ensureParticipantVnbPlanSnapshot(Employee $employee, Carbon $periodStart, $frameworkItems): ?VnbPlan
    {
        $existingPlan = VnbPlan::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if ($existingPlan) {
            return $existingPlan;
        }

        $periods = $this->ensureParticipantVnbPeriods($employee, $periodStart);
        $phaseOnePeriod = $periods->firstWhere('phase_number', 1) ?? $periods->first();

        if (!$phaseOnePeriod) {
            throw new \RuntimeException('Periode VnB tidak dapat dibuat untuk employee ini.');
        }

        $plan = VnbPlan::create([
            'employee_id' => $employee->id,
            'period_id' => $phaseOnePeriod->id,
            'phase_number' => $phaseOnePeriod->phase_number,
            'title' => 'Rencana VnB - ' . $employee->name,
            'description' => 'Auto-generated dari framework ' . $employee->getCareerStageCode(),
            'planning_mode' => 'adjust_all',
            'status' => 'draft',
        ]);

        $itemsToInsert = [];
        foreach ($frameworkItems as $phaseNumber => $items) {
            foreach ($items as $item) {
                $integrationParts = [];
                if ($item->integration_1) {
                    $integrationParts[] = $item->integration_1;
                }
                if ($item->integration_2) {
                    $integrationParts[] = $item->integration_2;
                }

                $itemsToInsert[] = [
                    'plan_id' => $plan->id,
                    'framework_item_id' => $item->id,
                    'activity_title' => $item->behaviour . ' - Phase ' . $phaseNumber,
                    'description' => !empty($integrationParts) ? implode(' | ', $integrationParts) : 'Activity for ' . $item->behaviour,
                    'integration_1' => $item->integration_1,
                    'integration_2' => $item->integration_2,
                    'due_date' => now()->addDays(7)->format('Y-m-d'),
                    'activity_date' => now()->addDays(7)->format('Y-m-d'),
                    'deliverables' => '-',
                    'behavior_metrics' => json_encode([$item->behaviour, 'phase_' . $phaseNumber]),
                    'submission_status' => 'draft',
                    'status' => 'draft',
                    'completion_percentage' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($itemsToInsert)) {
            VnbPlanItem::insert($itemsToInsert);
        }

        return $plan->fresh(['items', 'period']);
    }

    /**
     * @return \Illuminate\Support\Collection<int, VnbPeriod>
     */
    private function ensureParticipantVnbPeriods(Employee $employee, Carbon $periodStart)
    {
        $periods = collect();

        for ($phase = 1; $phase <= 3; $phase++) {
            $start = $periodStart->copy()->addMonths(($phase - 1) * 4);
            $end = $phase === 3
                ? $periodStart->copy()->addYear()->subDay()
                : $start->copy()->addMonths(4)->subDay();

            $periods->push(VnbPeriod::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'phase_number' => $phase,
                ],
                [
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'cutoff_date' => $end->copy()->setDay(min(25, $end->daysInMonth))->toDateString(),
                    'status' => now()->lt($start) ? 'not_started' : (now()->lte($end) ? 'in_progress' : 'completed'),
                ]
            ));
        }

        return $periods;
    }
}
