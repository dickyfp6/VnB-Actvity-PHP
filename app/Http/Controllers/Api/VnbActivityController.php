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
use Illuminate\Support\Facades\Storage;

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

        $activeAssignment = VnbActivityAssignment::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $periodStart = $employee->induction_date
            ?? $employee->vnb_period_start
            ?? $activeAssignment?->induction_date;

        if ($periodStart) {
            $this->ensureParticipantVnbPeriods($employee, Carbon::parse($periodStart));
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

        if (!in_array($item->submission_status, ['draft', 'revision_required'], true) && !$this->isCurrentActivityPhaseEditable($item)) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas sudah disubmit dan tidak dapat diubah.',
            ], 422);
        }

        $validated = $request->validate([
            'row_index' => 'required|integer|min:0',
            'activity_description' => 'required|string',
            'activity_date' => 'required|string',
        ]);

        $rowIndex = (int) $validated['row_index'];
        $rows = $this->normalizeActivityRows($item);

        if (!isset($rows[$rowIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Baris integrasi tidak ditemukan.',
            ], 404);
        }

        if (trim((string) $validated['activity_description']) === '' || trim((string) $validated['activity_date']) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom implementasi harus diisi sebelum dikirim.',
            ], 422);
        }

        $hasEvidenceForRow = $item->evidences()
            ->where('description', 'Integration ' . $rowIndex)
            ->exists();

        if (!$hasEvidenceForRow) {
            return response()->json([
                'success' => false,
                'message' => 'Setiap baris integrasi wajib memiliki bukti implementasi.',
            ], 422);
        }

        $rows[$rowIndex]['activity_description'] = trim($validated['activity_description']);
        $rows[$rowIndex]['activity_date'] = $this->normalizeDateValue($validated['activity_date']);
        $rows[$rowIndex]['submission_status'] = 'waiting_approval';
        $rows[$rowIndex]['submitted_at'] = now()->toDateTimeString();
        $rows[$rowIndex]['revision_notes'] = null;

        $this->persistActivityRows($item, $rows);

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

        if (!in_array($item->submission_status, ['draft', 'revision_required'], true) && !$this->isCurrentActivityPhaseEditable($item)) {
            return response()->json(['success' => false, 'message' => 'Status tidak memungkinkan perubahan.'], 422);
        }

        $validated = $request->validate([
            'row_index' => 'required|integer|min:0',
            'activity_description' => 'nullable|string',
            'activity_date' => 'nullable|string',
        ]);

        $rowIndex = (int) $validated['row_index'];
        $rows = $this->normalizeActivityRows($item);

        if (!isset($rows[$rowIndex])) {
            return response()->json(['success' => false, 'message' => 'Baris integrasi tidak ditemukan.'], 404);
        }

        $rows[$rowIndex]['activity_description'] = trim((string) ($validated['activity_description'] ?? ''));
        $rows[$rowIndex]['activity_date'] = $this->normalizeDateValue((string) ($validated['activity_date'] ?? ''));
        $rows[$rowIndex]['submission_status'] = 'draft';

        $this->persistActivityRows($item, $rows);

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

        $validated = $request->validate([
            'row_index' => 'nullable|integer|min:0',
        ]);

        if (array_key_exists('row_index', $validated) && $validated['row_index'] !== null) {
            $rowIndex = (int) $validated['row_index'];
            $rows = $this->normalizeActivityRows($item);

            if (!isset($rows[$rowIndex])) {
                return response()->json(['success' => false, 'message' => 'Baris integrasi tidak ditemukan.'], 404);
            }

            $rowStatus = strtolower((string) ($rows[$rowIndex]['submission_status'] ?? 'draft'));
            if (!in_array($rowStatus, ['waiting_approval', 'submitted'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Baris aktivitas belum diajukan oleh employee.',
                ], 422);
            }

            $user = Auth::user();
            $manager = Manager::where('email', $user->email)->first();
            $employee = $item->plan->employee;

            $isFunctional   = $manager && $employee->manager_functional_id == $manager->id;
            $isOperational  = $manager && $employee->manager_operational_id == $manager->id;

            if (!$isFunctional && !$isOperational) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otorisasi untuk approve aktivitas employee ini.',
                ], 403);
            }

            if ($isFunctional && !($rows[$rowIndex]['approved_functional_by'] ?? null)) {
                $rows[$rowIndex]['approved_functional_by'] = $user->id;
                $rows[$rowIndex]['approved_functional_at'] = now()->toDateTimeString();
            }

            if ($isOperational && !($rows[$rowIndex]['approved_operational_by'] ?? null)) {
                $rows[$rowIndex]['approved_operational_by'] = $user->id;
                $rows[$rowIndex]['approved_operational_at'] = now()->toDateTimeString();
            }

            // Business rule: one manager approval is enough to mark the row completed.
            $rows[$rowIndex]['submission_status'] = 'completed';
            $rows[$rowIndex]['revision_notes'] = null;
            $rows[$rowIndex]['submitted_at'] = $rows[$rowIndex]['submitted_at'] ?? now()->toDateTimeString();

            $this->persistActivityRows($item, $rows);

            return response()->json([
                'success' => true,
                'message' => 'Baris aktivitas telah disetujui.',
                'data' => $this->formatActivityItem($item->fresh()),
            ]);
        }

        if (!in_array($item->submission_status, ['waiting_approval', 'submitted'], true)) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak dalam status waiting for approval.'], 422);
        }

        $user = Auth::user();
        $manager = Manager::where('email', $user->email)->first();
        $employee = $item->plan->employee;

        // Determine which manager role this user plays
        $isFunctional   = $manager && $employee->manager_functional_id == $manager->id;
        $isOperational  = $manager && $employee->manager_operational_id == $manager->id;

        if (!$isFunctional && !$isOperational) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki otorisasi untuk approve aktivitas employee ini.',
            ], 403);
        }

        if ($isFunctional && !$item->approved_functional_by) {
            $item->approved_functional_by = $user->id;
            $item->approved_functional_at = now();
        }

        if ($isOperational && !$item->approved_operational_by) {
            $item->approved_operational_by = $user->id;
            $item->approved_operational_at = now();
        }

        // Business rule: one manager approval is enough to mark completed.
        $item->submission_status = 'completed';
        $item->status = 'completed';
        $item->completion_percentage = 100;

        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas telah disetujui dan dinyatakan Completed.',
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
            'row_index' => 'nullable|integer|min:0',
        ]);

        if (array_key_exists('row_index', $validated) && $validated['row_index'] !== null) {
            $rowIndex = (int) $validated['row_index'];
            $rows = $this->normalizeActivityRows($item);

            if (!isset($rows[$rowIndex])) {
                return response()->json(['success' => false, 'message' => 'Baris integrasi tidak ditemukan.'], 404);
            }

            $rowStatus = strtolower((string) ($rows[$rowIndex]['submission_status'] ?? 'draft'));
            if (!in_array($rowStatus, ['waiting_approval', 'submitted'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Baris aktivitas belum diajukan oleh employee.',
                ], 422);
            }

            $rows[$rowIndex]['submission_status'] = 'revision_required';
            $rows[$rowIndex]['revision_notes'] = $validated['revision_notes'];
            $rows[$rowIndex]['approved_functional_by'] = null;
            $rows[$rowIndex]['approved_functional_at'] = null;
            $rows[$rowIndex]['approved_operational_by'] = null;
            $rows[$rowIndex]['approved_operational_at'] = null;

            $this->persistActivityRows($item, $rows);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan revisi baris berhasil dikirim ke Employee.',
                'data' => $this->formatActivityItem($item->fresh()),
            ]);
        }

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
        $activityRows = $this->normalizeActivityRows($item);

        $data = [
            'id'                   => $item->id,
            'plan_id'              => $item->plan_id,
            'activity_title'       => $item->activity_title,
            'description'          => $item->description,
            'deliverables'         => $item->deliverables,
            'activity_description' => $item->activity_description,
            'activity_date'        => $item->activity_date,
            'activity_rows'        => $activityRows,
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
            'evidences'            => $item->evidences->map(function ($ev) {
                $previewUrl = $ev->s3_url;
                if (!$previewUrl && $ev->file_path && Storage::disk('public')->exists($ev->file_path)) {
                    $previewUrl = url('/storage/' . ltrim((string) $ev->file_path, '/'));
                }

                return [
                    'id' => $ev->id,
                    'file_name' => $ev->file_name,
                    'file_type' => $ev->file_type,
                    'file_size' => $ev->file_size,
                    'description' => $ev->description,
                    's3_url' => $ev->s3_url,
                    'preview_url' => $previewUrl,
                ];
            })->toArray(),
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

    /**
     * Normalize activity rows from stored JSON or legacy newline-delimited fields.
     */
    private function normalizeActivityRows(VnbPlanItem $item): array
    {
        $integrationParts = collect(explode('|', (string) $item->description))
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->values();

        $descriptionParts = collect(explode("\n---\n", (string) $item->activity_description))
            ->map(fn ($value) => trim((string) $value))
            ->values();

        $dateParts = collect(explode("\n---\n", (string) $item->activity_date))
            ->map(fn ($value) => trim((string) $value))
            ->values();

        $storedRows = collect(is_array($item->activity_rows) ? $item->activity_rows : [])
            ->values();

        $rows = [];

        foreach ($integrationParts as $index => $integrationText) {
            $stored = $storedRows->get($index);
            $rowStatus = (string) ($stored['submission_status'] ?? $item->submission_status ?? 'draft');
            $rows[] = [
                'integration_index' => $index,
                'integration_text' => $integrationText,
                'activity_description' => $stored['activity_description'] ?? ($descriptionParts->get($index) ?? ''),
                'activity_date' => $stored['activity_date'] ?? ($dateParts->get($index) ?? ''),
                'submission_status' => $rowStatus,
                'revision_notes' => $stored['revision_notes'] ?? null,
                'submitted_at' => $stored['submitted_at'] ?? optional($item->submitted_at)->toDateTimeString(),
                'approved_functional_by' => $stored['approved_functional_by'] ?? null,
                'approved_functional_at' => $stored['approved_functional_at'] ?? null,
                'approved_operational_by' => $stored['approved_operational_by'] ?? null,
                'approved_operational_at' => $stored['approved_operational_at'] ?? null,
            ];
        }

        return $rows;
    }

    private function normalizeDateValue(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
            return sprintf('%s-%02d-%02d', $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function summarizeActivityRows(array $rows): array
    {
        $statuses = collect($rows)->pluck('submission_status')->map(fn ($status) => strtolower((string) $status));

        if ($statuses->isEmpty()) {
            return ['draft', null];
        }

        if ($statuses->every(fn ($status) => $status === 'completed')) {
            return ['completed', collect($rows)->pluck('approved_at')->filter()->last() ?? null];
        }

        if ($statuses->every(fn ($status) => in_array($status, ['waiting_approval', 'submitted'], true))) {
            return ['waiting_approval', collect($rows)->pluck('submitted_at')->filter()->last() ?? null];
        }

        if ($statuses->every(fn ($status) => $status === 'revision_required')) {
            return ['revision_required', collect($rows)->pluck('submitted_at')->filter()->last() ?? null];
        }

        return ['draft', null];
    }

    private function persistActivityRows(VnbPlanItem $item, array $rows): void
    {
        [$summaryStatus, $summarySubmittedAt] = $this->summarizeActivityRows($rows);

        $item->update([
            'activity_rows' => array_values($rows),
            'activity_description' => collect($rows)->map(fn ($row) => $row['activity_description'] !== '' ? $row['activity_description'] : '-')->implode("\n---\n"),
            'activity_date' => collect($rows)->map(fn ($row) => $row['activity_date'] !== '' ? $row['activity_date'] : '-')->implode("\n---\n"),
            'submission_status' => $summaryStatus,
            'submitted_at' => $summarySubmittedAt,
        ]);
    }

    private function isRowSubmittedStatus(?string $status): bool
    {
        return in_array(strtolower((string) $status), ['waiting_approval', 'submitted', 'completed'], true);
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
                    'activity_date' => null,
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
        // Try to determine phase durations from framework items (same logic as VnbPlanController::getPhasesList)
        $careerStageCode = $employee->getCareerStageCode();

        $phases = VnbFrameworkItem::where('career_stage', $careerStageCode)
            ->distinct('phase')
            ->pluck('phase')
            ->map(function (string $phase): array {
                preg_match('/Fase\s+(\d+)/i', $phase, $matches);

                return [
                    'phase' => $phase,
                    'phase_number' => isset($matches[1]) ? (int) $matches[1] : 999,
                ];
            })
            ->sortBy('phase_number')
            ->values();

        $periods = collect();

        // If no framework phases found, fallback to previous 3-phase 4-month grouping
        if ($phases->isEmpty()) {
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

        $phaseStart = $periodStart ? $periodStart->copy()->startOfDay() : now()->startOfDay();

        $phases->each(function (array $phaseData, int $index) use (&$phaseStart, $employee, $periods) {
            $phase = $phaseData['phase'];
            $phaseNum = $index + 1;
            $durationMonths = 1;

            // Parse duration from phase string
            if (preg_match('/Fase\s+(\d+)\s+\((\d+)\s+Bulan\)/i', $phase, $matches)) {
                $durationMonths = (int) $matches[2];
            } elseif (preg_match('/^(\d+)-(\d+)$/', $phase, $matches)) {
                $start = (int) $matches[1];
                $end = (int) $matches[2];
                $durationMonths = $end - $start + 1;
            } elseif (preg_match('/^(\d+)\+$/', $phase, $matches)) {
                $start = (int) $matches[1];
                $durationMonths = 12 - $start + 1;
            } elseif (preg_match('/^\d+$/', $phase)) {
                $durationMonths = 1;
            } else {
                if (preg_match('/\d+/', $phase, $matches)) {
                    $durationMonths = (int) $matches[0];
                }
                if ($durationMonths < 1) {
                    $durationMonths = 1;
                }
            }

            $start = $phaseStart->copy();
            $end = $start->copy()->addMonthsNoOverflow($durationMonths)->subDay();

            $periods->push(VnbPeriod::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'phase_number' => $phaseNum,
                ],
                [
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'cutoff_date' => $end->copy()->setDay(min(25, $end->daysInMonth))->toDateString(),
                    'status' => now()->lt($start) ? 'not_started' : (now()->lte($end) ? 'in_progress' : 'completed'),
                ]
            ));

            // Next phase start is day after this end
            $phaseStart = $end->copy()->addDay();
        });

        return $periods;
    }

    private function isCurrentActivityPhaseEditable(VnbPlanItem $item): bool
    {
        $phaseNumber = 1;
        if (preg_match('/Fase\s*(\d+)/i', (string) $item->activity_title, $matches)) {
            $phaseNumber = (int) $matches[1];
        }

        $period = VnbPeriod::query()
            ->where('employee_id', $item->plan?->employee_id)
            ->where('phase_number', $phaseNumber)
            ->first();

        return $period?->status === 'in_progress';
    }
}
