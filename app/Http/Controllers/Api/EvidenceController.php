<?php

namespace App\Http\Controllers\Api;

use App\Models\VnbPlanItem;
use App\Models\VnbEvidence;
use App\Models\VnbProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EvidenceController extends Controller
{
    /**
     * UC-07: Upload Evidence/Documentation
     */
    public function uploadEvidence(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_item_id' => 'required|exists:vnb_plan_items,id',
            'file' => 'required|file|max:10240',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');

        // TODO: Upload to Supabase Storage
        // $storagePath = Storage::disk('s3')->put('evidence', $file);
        // $s3_url = Storage::disk('s3')->url($storagePath);

        $evidence = VnbEvidence::create([
            'plan_item_id' => $validated['plan_item_id'],
            'uploaded_by' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => 'evidence/' . $file->hashName(), // placeholder
            'file_type' => $file->extension(),
            'file_size' => $file->getSize(),
            'description' => $validated['description'] ?? null,
            'status' => 'pending_verification',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evidence uploaded successfully',
            'data' => $evidence
        ], 201);
    }

    /**
     * UC-07: View Uploaded Evidences
     */
    public function listEvidences(VnbPlanItem $planItem): JsonResponse
    {
        $evidences = $planItem->evidences()
            ->with('uploadedBy')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $evidences->items(),
            'pagination' => [
                'total' => $evidences->total(),
                'per_page' => $evidences->perPage(),
            ]
        ]);
    }

    /**
     * UC-08: View & Update Progress per Behavior Item
     */
    public function updateProgress(Request $request, VnbPlanItem $planItem): JsonResponse
    {
        $validated = $request->validate([
            'behavior_progress' => 'required|array',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $progress = VnbProgress::updateOrCreate(
            [
                'plan_item_id' => $planItem->id,
                'employee_id' => auth()->id(),
            ],
            [
                'behavior_progress' => $validated['behavior_progress'],
                'progress_percentage' => $validated['progress_percentage'],
                'notes' => $validated['notes'] ?? null,
                'last_updated_at' => now(),
            ]
        );

        // Update plan item completion percentage  
        $planItem->update(['completion_percentage' => $validated['progress_percentage']]);

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'data' => $progress
        ]);
    }

    /**
     * UC-08: View Progress Summary
     */
    public function getProgressSummary(VnbPlanItem $planItem): JsonResponse
    {
        $progress = VnbProgress::where('plan_item_id', $planItem->id)
            ->with('employee')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'plan_item' => $planItem,
                'progress' => $progress,
                'evidences_count' => $planItem->evidences()->count(),
                'verified_evidences' => $planItem->evidences()->where('status', 'verified')->count(),
            ]
        ]);
    }

    /**
     * Verify Evidence (Manager Action)
     */
    public function verifyEvidence(Request $request, VnbEvidence $evidence): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string',
        ]);

        $evidence->update([
            'status' => $validated['status'],
            'verification_notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Evidence {$validated['status']} successfully",
            'data' => $evidence
        ]);
    }
}
