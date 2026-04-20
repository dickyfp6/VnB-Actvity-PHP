<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\VnbPlanController;
use App\Http\Controllers\Api\EvidenceController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\VnbFrameworkController;
use App\Http\Controllers\Api\IntercommController;
use App\Http\Controllers\Api\ManagerController;
use App\Http\Controllers\Api\VnbActivityController;
use App\Http\Controllers\Api\HrisController;
use App\Http\Controllers\Api\StarController;

Route::get('/health', function () { return response()->json(['status' => 'ok']); });

// Auth (public)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/verify-password', [AuthController::class, 'verifyPassword']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // ==================== BERANDA - HRIS ====================
    Route::prefix('beranda/hris')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [HrisController::class, 'index']);
        Route::get('/sync-pending', [HrisController::class, 'getPendingUpdates']);
        Route::post('/sync/{id}', [HrisController::class, 'syncToEmployee']);
        Route::post('/sync-batch', [HrisController::class, 'syncBatch']);
        Route::get('/history', [HrisController::class, 'getSyncHistory']);
    });

    // ==================== STAR ====================
    Route::prefix('star')->middleware(['auth:sanctum'])->group(function () {
        // Schema
        Route::get('/schema', [StarController::class, 'getSchema']);
        
        // Recognition (STAR recognitions by employees)
        Route::get('/recognition', [StarController::class, 'listRecognitions']);
        Route::post('/recognition', [StarController::class, 'createRecognition']);
        Route::get('/recognition/{id}', [StarController::class, 'showRecognition']);
        
        // Achievements (employee's submitted achievements)
        Route::get('/achievements', [StarController::class, 'listAchievements']);
        Route::post('/achievements', [StarController::class, 'submitAchievement']);
        Route::get('/achievements/{id}', [StarController::class, 'showAchievement']);
        
        // Approval (PCX, Intercomm, Direktur assign TTD & calculate points)
        Route::get('/approvals', [StarController::class, 'listApprovalsForMe']);
        Route::post('/approvals/{id}/assign-ttd', [StarController::class, 'assignSignature']);
        Route::post('/approvals/{id}/approve', [StarController::class, 'approve']);
        Route::post('/approvals/{id}/reject', [StarController::class, 'reject']);
        Route::get('/approvals/{id}/points', [StarController::class, 'calculatePoints']);
    });

    // ==================== UC004 - BERANDA/EMPLOYEES ====================
    Route::prefix('beranda/employees')->middleware(['auth:sanctum'])->group(function () {
        // Include legacy employees API
        Route::get('/', [EmployeeController::class, 'index']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/{employee}', [EmployeeController::class, 'show']);
        Route::put('/{employee}', [EmployeeController::class, 'update']);
        Route::delete('/{employee}', [EmployeeController::class, 'destroy']);
        Route::get('/manager-options', [EmployeeController::class, 'managerOptions']);
        Route::get('/import/template', [EmployeeController::class, 'downloadImportTemplate']);
        Route::post('/import/paste', [EmployeeController::class, 'importFromPaste']);
        Route::post('/import/file', [EmployeeController::class, 'importFromFile']);
        Route::post('/import/confirm', [EmployeeController::class, 'confirmImport']);
        Route::post('/{employee}/cancel-vnb', [EmployeeController::class, 'cancelVnb']);
        Route::post('/{employee}/lifecycle', [EmployeeController::class, 'updateLifecycle']);
        Route::post('/{employee}/reset-credential', [EmployeeController::class, 'resetCredential']);
    });

    // UC004 - Manage Employee (legacy API - keep for backward compatibility)
    Route::get('employees/manager-options', [EmployeeController::class, 'managerOptions']);
    Route::get('employees/import/template', [EmployeeController::class, 'downloadImportTemplate']);
    Route::post('employees/import/paste', [EmployeeController::class, 'importFromPaste']);
    Route::post('employees/import/file', [EmployeeController::class, 'importFromFile']);
    Route::post('employees/import/confirm', [EmployeeController::class, 'confirmImport']);
    Route::apiResource('employees', EmployeeController::class);
    Route::post('employees/{employee}/cancel-vnb', [EmployeeController::class, 'cancelVnb']);
    Route::post('employees/{employee}/lifecycle', [EmployeeController::class, 'updateLifecycle']);
    Route::post('employees/{employee}/reset-credential', [EmployeeController::class, 'resetCredential']);

    // ==================== UC005 - VNB PLANNING ====================
    Route::prefix('vnb-plans')->group(function () {
        Route::get('employee', [VnbPlanController::class, 'getOrCreateEmployeePlan']);
        Route::get('{plan}', [VnbPlanController::class, 'show']);
        Route::post('/', [VnbPlanController::class, 'store']);
        Route::put('{plan}', [VnbPlanController::class, 'update']);
        Route::post('{plan}/draft', [VnbPlanController::class, 'saveDraft']);
        Route::post('{plan}/submit-approval', [VnbPlanController::class, 'submitForApproval']);
        Route::post('{plan}/manager-review', [VnbPlanController::class, 'managerApproveReject']);
        Route::post('{plan}/mark-in-progress', [VnbPlanController::class, 'markInProgress']);
        Route::post('{plan}/submit-revision/{revision}', [VnbPlanController::class, 'submitRevisionChanges']);
        Route::post('{plan}/approve-all', [VnbPlanController::class, 'managerApproveAll']);
        Route::post('{plan}/save-revisions', [VnbPlanController::class, 'managerSaveRevisions']);
        Route::get('{plan}/feedback', [VnbPlanController::class, 'getPlanFeedback']);
    });

    // ==================== UC006 - VNB ACTIVITY ====================
    Route::get('/vnb-activities', [VnbActivityController::class, 'index']);
    Route::post('/vnb-activities/{planItem}/submit', [VnbActivityController::class, 'submit']);
    Route::post('/vnb-activities/{planItem}/draft', [VnbActivityController::class, 'saveDraft']);

    // ==================== UC007 - REVIEW & APPROVE ====================
    Route::get('/vnb-activities/pending-review', [VnbActivityController::class, 'pendingReview']);
    Route::post('/vnb-activities/{planItem}/approve', [VnbActivityController::class, 'approve']);
    Route::post('/vnb-activities/{planItem}/request-revision', [VnbActivityController::class, 'requestRevision']);

    // ==================== VNB PARTICIPANTS (PCX/Intercomm manage who has VnB access) ====================
    Route::prefix('vnb/participants')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [VnbActivityController::class, 'getParticipants']);
        Route::post('/{employee}/assign', [VnbActivityController::class, 'assignParticipant']);
        Route::post('/{employee}/revoke', [VnbActivityController::class, 'revokeParticipant']);
        Route::get('/activity/{activityId}/participants', [VnbActivityController::class, 'getActivityParticipants']);
    });

    // ==================== VNB APPROVAL (Manager approval portal) ====================
    Route::prefix('vnb/approval')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/requests', [ManagerController::class, 'myApprovalRequests']);
        Route::get('/summary', [ManagerController::class, 'myApprovalSummary']);
        Route::get('/plans/{planId}/revisions/history', [ManagerController::class, 'getRevisionHistory']);
        Route::post('/plans/{planId}/approve', [ManagerController::class, 'approvePlan']);
        Route::post('/plans/{planId}/request-revision', [ManagerController::class, 'requestRevision']);
        Route::post('/plans/{planId}/batch-review', [ManagerController::class, 'batchReviewPlanItems']);
        Route::post('/plans/{planId}/items/{itemId}/approve', [ManagerController::class, 'approvePlanningItem']);
        Route::post('/plans/{planId}/items/{itemId}/request-revision', [ManagerController::class, 'requestRevisionForItem']);
    });

    // ==================== EVIDENCE ====================
    Route::prefix('evidence')->group(function () {
        Route::post('upload', [EvidenceController::class, 'uploadEvidence']);
        Route::get('plan-item/{planItem}', [EvidenceController::class, 'listEvidences']);
        Route::put('plan-item/{planItem}/progress', [EvidenceController::class, 'updateProgress']);
        Route::post('{evidence}/verify', [EvidenceController::class, 'verifyEvidence']);
    });

    // ==================== IMPORT ====================
    Route::prefix('imports')->group(function () {
        Route::post('employees', [ImportController::class, 'importEmployees']);
        Route::get('{import}/status', [ImportController::class, 'getImportStatus']);
    });

    // ==================== DASHBOARD ====================
    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);

    // ==================== MASTER DATA ====================
    Route::prefix('beranda/master-data')->group(function () {
        Route::get('/', [MasterDataController::class, 'index']);
        Route::post('/', [MasterDataController::class, 'store']);
        Route::post('/bulk', [MasterDataController::class, 'bulkStore']);
        Route::put('/{id}', [MasterDataController::class, 'update']);
        Route::delete('/{id}', [MasterDataController::class, 'destroy']);
    });

    // UC009 - Master Data (legacy API - keep for backward compatibility)
    Route::get('/master/{category}', [MasterDataController::class, 'index']);
    Route::post('/master/{category}', [MasterDataController::class, 'store']);
    Route::post('/master/{category}/bulk', [MasterDataController::class, 'bulkStore']);
    Route::put('/master/{category}/{id}', [MasterDataController::class, 'update']);
    Route::delete('/master/{category}/{id}', [MasterDataController::class, 'destroy']);

    // ==================== VNB FRAMEWORK ====================
    Route::prefix('beranda/vnb-framework')->group(function () {
        Route::get('/', [VnbFrameworkController::class, 'index']);
        Route::post('/upsert', [VnbFrameworkController::class, 'upsert']);
        Route::post('/clone', [VnbFrameworkController::class, 'clone']);
    });

    // UC002 - V&B Framework (legacy API - keep for backward compatibility)
    Route::get('/vnb-framework', [VnbFrameworkController::class, 'index']);
    Route::post('/vnb-framework/upsert', [VnbFrameworkController::class, 'upsert']);
    Route::post('/vnb-framework/clone', [VnbFrameworkController::class, 'clone']);

    // ==================== INTERCOMM ====================
    Route::prefix('beranda/intercomm')->group(function () {
        Route::get('/', [IntercommController::class, 'index']);
        Route::post('/', [IntercommController::class, 'store']);
        Route::put('/{id}', [IntercommController::class, 'update']);
        Route::post('/{id}/deactivate', [IntercommController::class, 'deactivate']);
        Route::post('/{id}/activate', [IntercommController::class, 'activate']);
    });

    // UC001 - Manage Intercomm (legacy API - keep for backward compatibility)
    Route::get('/intercomm', [IntercommController::class, 'index']);
    Route::post('/intercomm', [IntercommController::class, 'store']);
    Route::put('/intercomm/{id}', [IntercommController::class, 'update']);
    Route::post('/intercomm/{id}/deactivate', [IntercommController::class, 'deactivate']);
    Route::post('/intercomm/{id}/activate', [IntercommController::class, 'activate']);

    // ==================== MANAGERS ====================
    Route::prefix('beranda/managers')->group(function () {
        Route::get('/', [ManagerController::class, 'index']);
        Route::get('/{id}', [ManagerController::class, 'show']);
        Route::post('/', [ManagerController::class, 'store']);
        Route::put('/{id}', [ManagerController::class, 'update']);
        Route::delete('/{id}', [ManagerController::class, 'destroy']);
        Route::post('/{id}/reset-credential', [ManagerController::class, 'resetCredential']);
        Route::get('/{id}/employees', [ManagerController::class, 'employees']);
    });

    // UC003 - Manage Manager (legacy API - keep for backward compatibility)
    Route::get('/managers', [ManagerController::class, 'index']);
    Route::get('/managers/{id}', [ManagerController::class, 'show']);
    Route::post('/managers', [ManagerController::class, 'store']);
    Route::put('/managers/{id}', [ManagerController::class, 'update']);
    Route::delete('/managers/{id}', [ManagerController::class, 'destroy']);
    Route::post('/managers/{id}/reset-credential', [ManagerController::class, 'resetCredential']);
    Route::get('/managers/{id}/employees', [ManagerController::class, 'employees']);

    // ==================== MANAGER PORTAL (My Employees & Approvals) ====================
    Route::prefix('manager')->group(function () {
        Route::get('/employees', [ManagerController::class, 'myEmployees']);
        Route::get('/employees/{employeeId}', [ManagerController::class, 'myEmployeeDetail']);
        Route::get('/employees/{employeeId}/planning-history', [ManagerController::class, 'myEmployeePlanningHistory']);
        Route::get('/approval-requests', [ManagerController::class, 'myApprovalRequests']);
        Route::get('/approval-summary', [ManagerController::class, 'myApprovalSummary']);
        Route::get('/profile', [ManagerController::class, 'getMyProfile']);
        Route::put('/profile', [ManagerController::class, 'updateMyProfile']);
        Route::post('/plans/{planId}/request-revision', [ManagerController::class, 'requestRevision']);
        Route::post('/plans/{planId}/approve', [ManagerController::class, 'approvePlan']);
        Route::get('/plans/{planId}/revisions/history', [ManagerController::class, 'getRevisionHistory']);
        Route::get('/my-employee-revisions', [ManagerController::class, 'myEmployeeRevisions']);
        Route::post('/plans/{planId}/batch-review', [ManagerController::class, 'batchReviewPlanItems']);
        Route::post('/plans/{planId}/items/{itemId}/approve', [ManagerController::class, 'approvePlanningItem']);
        Route::post('/plans/{planId}/items/{itemId}/request-revision', [ManagerController::class, 'requestRevisionForItem']);
        Route::get('/pending-revisions', [ManagerController::class, 'getEmployeePendingRevisions']);
    });
});

