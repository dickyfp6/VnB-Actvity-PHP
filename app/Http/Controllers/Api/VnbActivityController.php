<?php

namespace App\Http\Controllers\Api;

use App\Models\VnbPlanItem;
use App\Models\VnbPlan;
use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VnbActivityController extends Controller
{
    /**
     * UC006: List activities for the current Employee (active phase only)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Find Employee employee linked to this user by email
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data Employee tidak ditemukan'], 404);
        }

        // Get plan items for all approved plans
        $items = VnbPlanItem::whereHas('plan', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id)
              ->where('status', 'approved');
        })
        ->with(['plan.employee.position'])
        ->get()
        ->map(function (VnbPlanItem $item): array {
            return $this->formatActivityItem($item);
        });

        return response()->json([
            'success' => true,
            'employee' => $employee->only('id', 'name', 'vnb_period_start', 'vnb_period_end', 'level'),
            'data' => $items,
        ]);
    }

    /**
     * UC006: Submit / save activity report by Employee
     */
    public function submit(Request $request, int $planItemId): JsonResponse
    {
        $item = VnbPlanItem::findOrFail($planItemId);

        // Only allow edit if draft or revision_required
        if (!in_array($item->submission_status, ['draft', 'revision_required'])) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas sudah disubmit dan tidak dapat diubah.',
            ], 422);
        }

        $validated = $request->validate([
            'activity_description' => 'required|string',
            'activity_date'        => 'required|date',
        ]);

        $item->update([
            'activity_description' => $validated['activity_description'],
            'activity_date'        => $validated['activity_date'],
            'submission_status'    => 'waiting_approval',
            'submitted_at'         => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil disubmit. Menunggu approval Manager.',
            'data'    => $this->formatActivityItem($item->fresh()),
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
            'activity_date'        => 'nullable|date',
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
            $items = VnbPlanItem::where('submission_status', 'waiting_approval')
                ->with(['plan.employee.position'])
                ->get()
                ->map(fn(VnbPlanItem $item): array => $this->formatActivityItem($item, withEmployee: true));
        } else {
            // Only Employees assigned to this manager
            $employeeIds = Employee::where('manager_functional_id', $manager->id)
                ->orWhere('manager_operational_id', $manager->id)
                ->pluck('id');

            $items = VnbPlanItem::where('submission_status', 'waiting_approval')
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

        if ($item->submission_status !== 'waiting_approval') {
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
        // TODO: Authorize: PCX, Intercomm only
        // TODO: Get all employees with VnB activity assignment
        // TODO: Show assignment status and dates
        
        return response()->json([
            'success' => true,
            'message' => 'VnB participants list',
            'data' => [],
        ]);
    }

    /**
     * Assign VnB access to an employee (make them a participant)
     */
    public function assignParticipant(Request $request, int $employeeId): JsonResponse
    {
        // TODO: Authorize: PCX, Intercomm only
        // TODO: Find employee by ID
        // TODO: Create/activate VnbActivityAssignment for this employee
        // TODO: Set is_active = true
        // TODO: Record who assigned and when
        
        return response()->json([
            'success' => true,
            'message' => 'Employee assigned to VnB Activity',
            'data' => [],
        ]);
    }

    /**
     * Revoke VnB access from an employee
     */
    public function revokeParticipant(Request $request, int $employeeId): JsonResponse
    {
        // TODO: Authorize: PCX, Intercomm only
        // TODO: Find employee by ID
        // TODO: Deactivate VnbActivityAssignment (set is_active = false)
        // TODO: Record who revoked and when
        
        return response()->json([
            'success' => true,
            'message' => 'Employee revoked from VnB Activity',
            'data' => [],
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
}
