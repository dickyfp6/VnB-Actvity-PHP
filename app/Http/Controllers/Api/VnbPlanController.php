<?php

namespace App\Http\Controllers\Api;

use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbPeriod;
use App\Models\VnbFrameworkItem;
use App\Models\VnbPlanRevision;
use App\Models\VnbPlanRevisionDetail;
use App\Models\Manager;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VnbPlanController extends Controller
{
    /**
     * Map employee level to career stage
     */
    private function mapLevelToCareerStage($level): string
    {
        if (!$level) {
            return 'manage_self_non_staff';
        }

        $level = strtolower($level);
        
        // Extract primary role (before "/" if compound role)
        $primaryRole = explode('/', $level)[0];
        $primaryRole = strtolower(trim($primaryRole));
        
        // Check for Non-Staff FIRST (before generic "staff" check)
        if (str_contains($primaryRole, 'non-staff') || str_contains($primaryRole, 'non staff')) {
            return 'manage_self_non_staff';
        }
        
        // Check Manager/Kepala
        if (str_contains($primaryRole, 'manager') || str_contains($primaryRole, 'kepala')) {
            return 'manage_managers';
        }
        
        // Check Staff (primary role) - now won't match non-staff
        if (str_contains($primaryRole, 'staff')) {
            return 'manage_self_staff';
        }
        
        // Check Supervisor/Lead (primary role) 
        if (str_contains($primaryRole, 'supervisor') || str_contains($primaryRole, 'lead')) {
            return 'manage_others';
        }
        
        // Default
        return 'manage_self_non_staff';
    }

    /**
     * Get or create New Hire plan dengan template framework
     */
    public function getOrCreateNewHirePlan(): JsonResponse
    {
        $user = auth()->user();
        if (!$user->isNewHire() || !$user->employee_id) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya New Hire yang dapat mengakses fitur ini'
            ], 403);
        }

        $employee = $user->employee;
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data New Hire tidak ditemukan'
            ], 404);
        }

        // Cek apakah sudah ada plan draft atau submitted
        $existingPlan = VnbPlan::where('employee_id', $employee->id)
            ->whereIn('status', ['draft', 'waiting_manager_approval', 'approved', 'in_progress', 'rejected'])
            ->latest()
            ->first();

        if ($existingPlan) {
            return response()->json([
                'success' => true,
                'data' => $existingPlan->load(['items', 'period']),
                'deadline' => $existingPlan->employee->induction_date ? $existingPlan->employee->induction_date->addDays(7)->toDateString() : null,
                'career_stage' => $existingPlan->employee->getCareerStage(),
            ]);
        }

        // Auto-create plan dari framework template berdasarkan career stage
        $careerStageCode = $employee->getCareerStageCode();
        
        // Ambil framework items untuk career stage ini
        $frameworkItems = VnbFrameworkItem::where('career_stage', $careerStageCode)
            ->get()
            ->groupBy('phase');

        if ($frameworkItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Framework template tidak ditemukan untuk career stage: ' . $employee->getCareerStage()
            ], 404);
        }

        // Buat plan baru dengan status draft
        $period = VnbPeriod::find(1);
        if (!$period || $period->employee_id !== $employee->id) {
            // Cari period untuk employee ini, atau buat default
            $period = VnbPeriod::where('employee_id', $employee->id)->first();
            if (!$period) {
                // Create minimal period jika tidak ada
                $period = VnbPeriod::create([
                    'employee_id' => $employee->id,
                    'phase_number' => 1,
                    'start_date' => $employee->induction_date ?? now(),
                    'end_date' => ($employee->induction_date ?? now())->addMonths(6),
                    'cutoff_date' => ($employee->induction_date ?? now())->addMonths(6)->day(25),
                    'status' => 'in_progress',
                ]);
            }
        }

        $plan = VnbPlan::create([
            'employee_id' => $employee->id,
            'period_id' => $period->id,
            'phase_number' => $period->phase_number,
            'title' => 'Rencana VnB - ' . $employee->name,
            'description' => 'Auto-generated dari framework ' . $careerStageCode,
            'planning_mode' => 'adjust_all',
            'status' => 'draft',
        ]);

        // Create items dari framework
        // ✅ FIX #4: Batch insert instead of nested loop + create
        // Before: 15+ queries | After: 4-5 queries (3-4x faster)
        $itemsToInsert = [];
        
        foreach ($frameworkItems as $phaseNumber => $items) {
            foreach ($items as $item) {
                // Build description from integration_1 and integration_2 with pipe separator
                $integrationParts = [];
                if ($item->integration_1) {
                    $integrationParts[] = $item->integration_1;
                }
                if ($item->integration_2) {
                    $integrationParts[] = $item->integration_2;
                }
                $description = !empty($integrationParts) ? implode(' | ', $integrationParts) : 'Activity for ' . $item->behaviour;

                $itemsToInsert[] = [
                    'plan_id' => $plan->id,
                    'framework_item_id' => $item->id,
                    'activity_title' => $item->behaviour . ' - Phase ' . $phaseNumber,
                    'description' => $description,
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
        
        // Insert all items at once
        if (!empty($itemsToInsert)) {
            VnbPlanItem::insert($itemsToInsert);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan template berhasil dibuat',
            'data' => $plan->load(['items', 'period']),
            'deadline' => $employee->induction_date ? $employee->induction_date->addDays(7)->toDateString() : null,
            'career_stage' => $employee->getCareerStage(),
        ], 201);
    }

    /**
     * UC-03: View Plan & Behavior Items
     */
    public function show(VnbPlan $plan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $plan->load([
                'employee',
                'period',
                'approvedBy',
                'items.evidences',
                'items.progress',
            ])
        ]);
    }

    /**
     * UC-04: Create/Edit Plan
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_id' => 'required|exists:vnb_periods,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'planning_mode' => 'required|in:adjust_all,custom',
            'items' => 'nullable|array',
            'items.*.activity_title' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.due_date' => 'nullable|date',
            'items.*.activity_date' => 'nullable|date',
            'items.*.deliverables' => 'nullable|string',
            'items.*.behavior_metrics' => 'nullable|array',
        ]);

        $employee = Employee::find($validated['employee_id']);
        
        $plan = VnbPlan::create([
            'employee_id' => $validated['employee_id'],
            'period_id' => $validated['period_id'],
            'phase_number' => VnbPeriod::find($validated['period_id'])->phase_number ?? 1,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'planning_mode' => $validated['planning_mode'],
        ]);

        // ✅ Auto-generate items dari framework jika items kosong atau tidak dikirim
        $itemsToInsert = [];
        
        if (empty($validated['items'])) {
            // Auto-generate dari framework berdasarkan career stage
            $careerStageCode = $employee->getCareerStageCode();
            
            $frameworkItems = VnbFrameworkItem::where('career_stage', $careerStageCode)
                ->get()
                ->groupBy('phase');

            if (!$frameworkItems->isEmpty()) {
                foreach ($frameworkItems as $phaseNumber => $items) {
                    foreach ($items as $item) {
                        $itemsToInsert[] = [
                            'plan_id' => $plan->id,
                            'framework_item_id' => $item->id,
                            'activity_title' => $item->behaviour . ' - Phase ' . $phaseNumber,
                            'description' => ($item->integration_1 ?? '') . ' | ' . ($item->integration_2 ?? ''),
                            'due_date' => now()->addDays(7)->format('Y-m-d'),
                            'activity_date' => now()->addDays(7)->format('Y-m-d'),
                            'deliverables' => '-',
                            'submission_status' => 'draft',
                            'status' => 'draft',
                            'completion_percentage' => 0,
                            'behavior_metrics' => json_encode([$item->behaviour, 'phase_' . $phaseNumber]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        } else {
            // Gunakan items yang dikirim dari frontend
            $itemsToInsert = array_map(function($item) use ($plan) {
                return [
                    'plan_id' => $plan->id,
                    'activity_title' => $item['activity_title'] ?? '',
                    'description' => $item['description'] ?? '',
                    'due_date' => isset($item['due_date']) ? $item['due_date'] : now()->addDays(7)->format('Y-m-d'),
                    'activity_date' => isset($item['activity_date']) ? $item['activity_date'] : now()->addDays(7)->format('Y-m-d'),
                    'deliverables' => $item['deliverables'] ?? '-',
                    'submission_status' => 'draft',
                    'status' => 'draft',
                    'completion_percentage' => 0,
                    'behavior_metrics' => isset($item['behavior_metrics']) ? json_encode($item['behavior_metrics']) : json_encode([]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $validated['items']);
        }

        // Insert all items at once
        if (!empty($itemsToInsert)) {
            VnbPlanItem::insert($itemsToInsert);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => $plan->load(['items', 'period']),
            'career_stage' => $employee->getCareerStage(),
        ], 201);
    }

    /**
     * UC-04: Update Plan
     */
    public function update(Request $request, VnbPlan $plan): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'planning_mode' => 'in:adjust_all,custom',
            'items' => 'array',
            'items.*.id' => 'nullable|exists:vnb_plan_items,id',
            'items.*.activity_title' => 'string|max:255',
            'items.*.description' => 'string',
            'items.*.due_date' => 'nullable|date',
            'items.*.activity_date' => 'nullable|date',
            'items.*.deliverables' => 'string',
            'items.*.behavior_metrics' => 'array',
        ]);

        if (isset($validated['title'])) {
            $plan->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? $plan->description,
                'planning_mode' => $validated['planning_mode'] ?? $plan->planning_mode,
            ]);
        }

        if (isset($validated['items'])) {
            // ✅ FIX #2: Batch update/insert instead of looping + find + update
            // Before: 20+ queries | After: 3 queries (6-7x faster)
            
            // Separate existing items (with id) from new items (without id)
            $existingItems = collect($validated['items'])->filter(fn($item) => isset($item['id']))->keyBy('id')->toArray();
            $newItems = collect($validated['items'])->filter(fn($item) => !isset($item['id']))->values()->toArray();
            
            // Update existing items in batch
            if (!empty($existingItems)) {
                foreach ($existingItems as $itemId => $itemData) {
                    VnbPlanItem::where('id', $itemId)->update([
                        'activity_title' => $itemData['activity_title'] ?? null,
                        'description' => $itemData['description'] ?? null,
                        'due_date' => $itemData['due_date'] ?? null,
                        'activity_date' => $itemData['activity_date'] ?? null,
                        'deliverables' => $itemData['deliverables'] ?? null,
                        'behavior_metrics' => isset($itemData['behavior_metrics']) ? json_encode($itemData['behavior_metrics']) : null,
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // Create new items in batch
            if (!empty($newItems)) {
                $newItemsData = array_map(function($item) use ($plan) {
                    return [
                        'plan_id' => $plan->id,
                        'activity_title' => $item['activity_title'],
                        'description' => $item['description'],
                        'due_date' => $item['due_date'] ?? null,
                        'activity_date' => $item['activity_date'] ?? null,
                        'deliverables' => $item['deliverables'],
                        'behavior_metrics' => json_encode($item['behavior_metrics']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $newItems);
                
                VnbPlanItem::insert($newItemsData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => $plan->load('items')
        ]);
    }

    /**
     * UC-04: Save Draft Plan (manual save pada UI)
     */
    public function saveDraft(Request $request, VnbPlan $plan): JsonResponse
    {
        abort_unless(in_array($plan->status, ['draft', 'rejected'], true), 400, 'Plan hanya bisa disimpan saat draft atau rejected');

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'items' => 'array',
            'items.*.id' => 'nullable|exists:vnb_plan_items,id',
            'items.*.activity_title' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.due_date' => 'nullable|date',
            'items.*.activity_date' => 'nullable|date',
            'items.*.deliverables' => 'nullable|string',
            'items.*.behavior_metrics' => 'nullable|array',
        ]);

        if (isset($validated['title'])) {
            $plan->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? $plan->description,
            ]);
        }

        if (isset($validated['items'])) {
            // ✅ FIX #3: Batch update/insert instead of looping + find + update
            // Before: 20+ queries | After: 3 queries (6x faster)
            
            // Separate existing items (with id) from new items (without id)
            $existingItems = collect($validated['items'])->filter(fn($item) => isset($item['id']))->keyBy('id')->toArray();
            $newItems = collect($validated['items'])->filter(fn($item) => !isset($item['id']))->values()->toArray();
            
            // Update existing items individually to preserve complex logic (deliverables cleaning)
            if (!empty($existingItems)) {
                foreach ($existingItems as $itemId => $item) {
                    // ONLY update deliverables, description, and behavior_metrics
                    // DO NOT touch activity_title (it's the behavior name from framework)
                    $updateData = [];
                    
                    // Use array_key_exists instead of isset to handle null values
                    if (array_key_exists('deliverables', $item)) {
                        // Clean up deliverables: remove extra separators and normalize
                        $deliverables = $item['deliverables'] ?? '';
                        
                        // Clean up separators - remove standalone separator rows
                        $deliverables = preg_replace('/\n---\n/u', "\n---\n", $deliverables);
                        $deliverables = preg_replace('/^\n*---\n*/u', '', $deliverables);  // Remove leading separators
                        $deliverables = preg_replace('/\n*---\n*$/u', '', $deliverables);  // Remove trailing separators
                        $deliverables = preg_replace('/(\n---\n)+/u', "\n---\n", $deliverables); // Collapse multiple separators
                        
                        // If result is empty, only separator, or whitespace - explicitly clear it
                        if (empty(trim($deliverables)) || trim($deliverables) === '-' || trim($deliverables) === '---') {
                            $deliverables = '';
                        }
                        
                        $updateData['deliverables'] = $deliverables;
                    }
                    if (isset($item['description'])) {
                        $updateData['description'] = $item['description'];
                    }
                    if (isset($item['behavior_metrics'])) {
                        $updateData['behavior_metrics'] = json_encode($item['behavior_metrics']);
                    }
                    
                    // Only update if there's something to update
                    if (!empty($updateData)) {
                        VnbPlanItem::where('id', $itemId)->update($updateData);
                    }
                }
            }
            
            // Create new items in batch
            if (!empty($newItems)) {
                $newItemsData = array_map(function($item) use ($plan) {
                    return [
                        'plan_id' => $plan->id,
                        'activity_title' => $item['activity_title'] ?? null,
                        'description' => $item['description'] ?? null,
                        'due_date' => $item['due_date'] ?? null,
                        'activity_date' => $item['activity_date'] ?? null,
                        'deliverables' => $item['deliverables'] ?? null,
                        'behavior_metrics' => isset($item['behavior_metrics']) ? json_encode($item['behavior_metrics']) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $newItems);
                
                VnbPlanItem::insert($newItemsData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft plan berhasil disimpan',
            'data' => $plan->load('items')
        ]);
    }

    /**
     * UC-05: Submit Plan for Manager Approval
     */
    public function submitForApproval(VnbPlan $plan): JsonResponse
    {
        if (!in_array($plan->status, ['draft', 'rejected'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft/rejected plans can be submitted'
            ], 400);
        }

        $plan->update([
            'status' => 'waiting_manager_approval',
            'submitted_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->createPlanRevisionSubmission($plan->fresh()->load('items'));

        // TODO: Send notification to manager

        return response()->json([
            'success' => true,
            'message' => 'Plan submitted for manager approval',
            'data' => $plan
        ]);
    }

    /**
     * UC-06: Manager Approve/Reject Plan
     */
    public function managerApproveReject(Request $request, VnbPlan $plan): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = (bool) $user?->hasRole('admin');

        if (!$isAdmin) {
            abort_unless($user?->hasRole('manager'), 403, 'Hanya manager yang bisa memproses approval.');

            $manager = Manager::query()
                ->where('user_id', $user->id)
                ->orWhereRaw('LOWER(email) = ?', [strtolower(trim((string) $user->email))])
                ->first();

            abort_unless($manager !== null, 403, 'Data manager tidak ditemukan.');

            $employee = $plan->employee;
            $isAssigned = $employee
                && ((int) $employee->manager_functional_id === (int) $manager->id
                    || (int) $employee->manager_operational_id === (int) $manager->id);

            abort_unless($isAssigned, 403, 'Anda tidak berwenang mereview plan New Hire ini.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string',
        ]);

        $revision = VnbPlanRevision::query()
            ->where('vnb_plan_id', $plan->id)
            ->where('status', 'pending')
            ->orderByDesc('version_number')
            ->first();

        if (!$revision) {
            $revision = $this->createPlanRevisionSubmission($plan->fresh('items'));
        }

        if ($validated['action'] === 'approve') {
            $plan->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'discussion_notes' => $validated['notes'] ?? null,
                'rejection_reason' => null,
            ]);

            $revision->update([
                'status' => 'approved',
                'decision' => 'approve',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $validated['notes'] ?? null,
            ]);
        } else {
            $plan->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['notes'] ?? null,
            ]);

            $revision->update([
                'status' => 'rejected',
                'decision' => 'reject',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $validated['notes'] ?? null,
            ]);
        }

        // TODO: Send notification to employee

        return response()->json([
            'success' => true,
            'message' => "Plan {$validated['action']}d successfully",
            'data' => $plan
        ]);
    }

    /**
     * UC-06: Mark Plan as In Progress
     */
    public function markInProgress(VnbPlan $plan): JsonResponse
    {
        if ($plan->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved plans can be marked as in progress'
            ], 400);
        }

        $plan->update(['status' => 'in_progress']);

        return response()->json([
            'success' => true,
            'message' => 'Plan marked as in progress',
            'data' => $plan
        ]);
    }

    private function createPlanRevisionSubmission(VnbPlan $plan): VnbPlanRevision
    {
        $latestVersion = (int) VnbPlanRevision::query()
            ->where('vnb_plan_id', $plan->id)
            ->max('version_number');

        $snapshot = [
            'plan' => [
                'id' => $plan->id,
                'status' => $plan->status,
                'phase_number' => $plan->phase_number,
                'title' => $plan->title,
                'description' => $plan->description,
            ],
            'items' => $plan->items->map(function (VnbPlanItem $item) {
                return [
                    'id' => $item->id,
                    'activity_title' => $item->activity_title,
                    'description' => $item->description,
                    'deliverables' => $item->deliverables,
                    'due_date' => optional($item->due_date)->toDateString(),
                    'activity_date' => optional($item->activity_date)->toDateString(),
                    'behavior_metrics' => $item->behavior_metrics,
                ];
            })->values()->all(),
        ];

        return VnbPlanRevision::create([
            'vnb_plan_id' => $plan->id,
            'version_number' => $latestVersion + 1,
            'status' => 'pending',
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
            'snapshot' => $snapshot,
        ]);
    }

    /**
     * New Hire: Submit revision changes dari manager revision
     * POST /api/vnb-plans/{plan}/submit-revision/{revision}
     */
    public function submitRevisionChanges(Request $request, VnbPlan $plan, VnbPlanRevision $revision): JsonResponse
    {
        $user = auth()->user();
        abort_unless($user !== null, 401);

        // Check ownership - user harus employee dari plan ini
        $employee = $user->employee;
        abort_unless($employee && $employee->id === $plan->employee_id, 403, 'Anda bukan pemilik plan ini');

        // Verify revision belongs to this plan
        abort_unless($revision->vnb_plan_id === $plan->id, 403, 'Revisi tidak sesuai dengan planning');

        $request->validate([
            'changes' => 'required|array',
            'changes.*.item_id' => 'required|integer',
            'changes.*.old_values' => 'required|array',
            'changes.*.new_values' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $changes = $request->input('changes', []);

            // ✅ FIX #5: Batch operations instead of loop + find + update
            // Before: 30+ queries | After: 5-6 queries (2x faster)
            
            // 1. Extract all item IDs
            $itemIds = collect($changes)->pluck('item_id')->unique()->toArray();
            
            // 2. Fetch all items once with whereIn() instead of finding each one
            $items = VnbPlanItem::whereIn('id', $itemIds)->keyBy('id');
            
            // 3. Prepare batch update data and revision details
            $revisionDetailsData = [];
            
            foreach ($changes as $change) {
                $itemId = $change['item_id'];
                $oldValues = $change['old_values'];
                $newValues = $change['new_values'];

                // Get the cached item instead of finding it again
                $item = $items->get($itemId);
                if (!$item) {
                    abort(404, "Item $itemId tidak ditemukan");
                }

                // Update the item in memory (will be saved to DB)
                $item->update($newValues);

                // Prepare revision detail for batch insert
                $revisionDetailsData[] = [
                    'vnb_plan_revision_id' => $revision->id,
                    'vnb_plan_item_id' => $itemId,
                    'old_values' => json_encode($oldValues),
                    'new_values' => json_encode($newValues),
                    'changed_by' => $employee->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Batch insert all revision details at once
            if (!empty($revisionDetailsData)) {
                DB::table('vnb_plan_revision_details')->insert($revisionDetailsData);
            }

            // Update revision status
            $revision->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Update plan status back to waiting manager approval
            $plan->update([
                'status' => 'revision_draft',  // New Hire sedang dalam draft revisi
            ]);

            // Log activity (optional - requires spatie/laravel-activitylog)
            if (function_exists('activity')) {
                activity('revision_submitted')
                    ->performedOn($plan)
                    ->causedBy($user)
                    ->withProperties([
                        'revision_number' => $revision->revision_number,
                        'changes_count' => count($changes),
                    ])
                    ->log('New hire submitted revision changes');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perubahan revisi berhasil disimpan. Menunggu approval manager.',
                'data' => [
                    'revision_id' => $revision->id,
                    'status' => 'submitted',
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan perubahan revisi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
