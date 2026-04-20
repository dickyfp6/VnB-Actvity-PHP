<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StarController extends Controller
{
    /**
     * Get STAR schema (framework for STAR recognitions)
     */
    public function getSchema(): JsonResponse
    {
        // TODO: Return STAR schema definition
        // (S=Strength, T=Teamwork, A=Achievement, R=Reliability, etc.)
        
        return response()->json([
            'success' => true,
            'message' => 'STAR schema',
            'data' => [],
        ]);
    }

    /**
     * List all STAR recognitions visible to current user
     */
    public function listRecognitions(): JsonResponse
    {
        // Authorize: all roles
        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses recognitions.');
        
        // TODO: Return list of STAR recognitions
        
        return response()->json([
            'success' => true,
            'message' => 'List of STAR recognitions',
            'data' => [],
        ]);
    }

    /**
     * Create a new STAR recognition
     */
    public function createRecognition(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:employees,id',
            'category' => 'required|string',
            'description' => 'required|string',
        ]);

        // Authorize: employees can give recognition
        abort_unless(auth()->user()?->hasRole('employee'), 403, 'Hanya employee yang bisa memberikan recognition.');
        
        // TODO: Create new STAR recognition
        // TODO: Set status as pending approval
        
        return response()->json([
            'success' => true,
            'message' => 'STAR recognition created',
            'data' => [],
        ]);
    }

    /**
     * Show specific STAR recognition
     */
    public function showRecognition(int $id): JsonResponse
    {
        // TODO: Get recognition by ID
        // TODO: Check authorization
        
        return response()->json([
            'success' => true,
            'message' => 'STAR recognition details',
            'data' => [],
        ]);
    }

    /**
     * List employee's submitted achievements
     */
    public function listAchievements(): JsonResponse
    {
        // Authorize: all roles can view achievements
        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses achievements.');
        
        // TODO: Get current user's employee
        // TODO: Return list of submitted achievements
        
        return response()->json([
            'success' => true,
            'message' => 'List of achievements',
            'data' => [],
        ]);
    }

    /**
     * Submit a new achievement
     */
    public function submitAchievement(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'evidence' => 'nullable|string',
        ]);

        // Authorize: employees only
        abort_unless(auth()->user()?->hasRole('employee'), 403, 'Hanya employee yang bisa mengajukan achievement.');
        
        // TODO: Create new achievement
        // TODO: Set status as pending approval
        
        return response()->json([
            'success' => true,
            'message' => 'Achievement submitted',
            'data' => [],
        ]);
    }

    /**
     * Show specific achievement
     */
    public function showAchievement(int $id): JsonResponse
    {
        // TODO: Get achievement by ID
        // TODO: Check authorization
        
        return response()->json([
            'success' => true,
            'message' => 'Achievement details',
            'data' => [],
        ]);
    }

    /**
     * List pending approvals for current user
     */
    public function listApprovalsForMe(): JsonResponse
    {
        // TODO: Authorize: PCX, Intercomm, Direktur Utama only
        // TODO: Get current user
        // TODO: Return list of recognitions/achievements awaiting their approval
        
        return response()->json([
            'success' => true,
            'message' => 'Pending approvals for you',
            'data' => [],
        ]);
    }

    /**
     * Assign signature (TTD) to a recognition/achievement
     * Just mark as approved with signature, then calculate points
     */
    public function assignSignature(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Authorize: PCX, Intercomm, Direktur Utama only
        abort_unless(auth()->user()?->hasAnyRole(['pcx_manager', 'intercomm', 'direktur_utama']), 403, 'Hanya PCX, Intercomm, dan Direktur Utama yang bisa memberikan TTD.');
        
        // TODO: Get recognition/achievement by ID
        // TODO: Mark as approved and assign current user as signer
        // TODO: Set signature timestamp
        // TODO: Calculate points (see calculatePoints)
        
        return response()->json([
            'success' => true,
            'message' => 'Signature assigned and approval completed',
            'data' => [],
        ]);
    }

    /**
     * Approve a recognition/achievement
     * (Same as assignSignature for simple soft-file approval)
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        return $this->assignSignature($request, $id);
    }

    /**
     * Reject a recognition/achievement
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        // Authorize: PCX, Intercomm, Direktur Utama only
        abort_unless(auth()->user()?->hasAnyRole(['pcx_manager', 'intercomm', 'direktur_utama']), 403, 'Hanya PCX, Intercomm, dan Direktur Utama yang bisa menolak achievement.');
        
        // TODO: Get recognition/achievement by ID
        // TODO: Mark as rejected
        // TODO: Store rejection reason
        // TODO: Notify recipient/submitter
        
        return response()->json([
            'success' => true,
            'message' => 'Achievement rejected',
            'data' => [],
        ]);
    }

    /**
     * Calculate STAR points for a recognition/achievement
     * Based on category/schema (automatic system calculation)
     */
    public function calculatePoints(Request $request, int $id): JsonResponse
    {
        // TODO: Get recognition/achievement by ID
        // TODO: Load STAR schema/category
        // TODO: Calculate points based on category definition
        // TODO: Store points in database
        // TODO: Return calculated points
        
        return response()->json([
            'success' => true,
            'message' => 'STAR points calculated',
            'points' => 0,
            'data' => [],
        ]);
    }
}
