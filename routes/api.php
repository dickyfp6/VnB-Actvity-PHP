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

Route::get('/health', function () { return response()->json(['status' => 'ok']); });

// Auth (public)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // UC004 - Manage New Hire
    Route::get('employees/manager-options', [EmployeeController::class, 'managerOptions']);
    Route::get('employees/import/template', [EmployeeController::class, 'downloadImportTemplate']);
    Route::post('employees/import/paste', [EmployeeController::class, 'importFromPaste']);
    Route::post('employees/import/file', [EmployeeController::class, 'importFromFile']);
    Route::post('employees/import/confirm', [EmployeeController::class, 'confirmImport']);
    Route::apiResource('employees', EmployeeController::class);
    Route::post('employees/{employee}/cancel-vnb', [EmployeeController::class, 'cancelVnb']);
    Route::post('employees/{employee}/lifecycle', [EmployeeController::class, 'updateLifecycle']);
    Route::post('employees/{employee}/reset-credential', [EmployeeController::class, 'resetCredential']);

    // UC005 - VnB Planning
    Route::prefix('vnb-plans')->group(function () {
        Route::get('new-hire', [VnbPlanController::class, 'getOrCreateNewHirePlan']);
        Route::get('{plan}', [VnbPlanController::class, 'show']);
        Route::post('/', [VnbPlanController::class, 'store']);
        Route::put('{plan}', [VnbPlanController::class, 'update']);
        Route::post('{plan}/draft', [VnbPlanController::class, 'saveDraft']);
        Route::post('{plan}/submit-approval', [VnbPlanController::class, 'submitForApproval']);
        Route::post('{plan}/manager-review', [VnbPlanController::class, 'managerApproveReject']);
        Route::post('{plan}/mark-in-progress', [VnbPlanController::class, 'markInProgress']);
    });

    // UC006 - VnB Activity
    Route::get('/vnb-activities', [VnbActivityController::class, 'index']);
    Route::post('/vnb-activities/{planItem}/submit', [VnbActivityController::class, 'submit']);
    Route::post('/vnb-activities/{planItem}/draft', [VnbActivityController::class, 'saveDraft']);

    // UC007 - Review & Approve
    Route::get('/vnb-activities/pending-review', [VnbActivityController::class, 'pendingReview']);
    Route::post('/vnb-activities/{planItem}/approve', [VnbActivityController::class, 'approve']);
    Route::post('/vnb-activities/{planItem}/request-revision', [VnbActivityController::class, 'requestRevision']);

    // Evidence
    Route::prefix('evidence')->group(function () {
        Route::post('upload', [EvidenceController::class, 'uploadEvidence']);
        Route::get('plan-item/{planItem}', [EvidenceController::class, 'listEvidences']);
        Route::put('plan-item/{planItem}/progress', [EvidenceController::class, 'updateProgress']);
        Route::post('{evidence}/verify', [EvidenceController::class, 'verifyEvidence']);
    });

    // Import
    Route::prefix('imports')->group(function () {
        Route::post('employees', [ImportController::class, 'importEmployees']);
        Route::get('{import}/status', [ImportController::class, 'getImportStatus']);
    });

    // UC008 - Dashboard
    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);

    // UC009 - Master Data
    Route::get('/master/{category}', [MasterDataController::class, 'index']);
    Route::post('/master/{category}', [MasterDataController::class, 'store']);
    Route::post('/master/{category}/bulk', [MasterDataController::class, 'bulkStore']);
    Route::put('/master/{category}/{id}', [MasterDataController::class, 'update']);
    Route::delete('/master/{category}/{id}', [MasterDataController::class, 'destroy']);

    // UC002 - V&B Framework
    Route::get('/vnb-framework', [VnbFrameworkController::class, 'index']);
    Route::post('/vnb-framework/upsert', [VnbFrameworkController::class, 'upsert']);
    Route::post('/vnb-framework/clone', [VnbFrameworkController::class, 'clone']);

    // UC001 - Manage Intercomm
    Route::get('/intercomm', [IntercommController::class, 'index']);
    Route::post('/intercomm', [IntercommController::class, 'store']);
    Route::put('/intercomm/{id}', [IntercommController::class, 'update']);
    Route::post('/intercomm/{id}/deactivate', [IntercommController::class, 'deactivate']);
    Route::post('/intercomm/{id}/activate', [IntercommController::class, 'activate']);

    // UC003 - Manage Manager
    Route::get('/managers', [ManagerController::class, 'index']);
    Route::get('/managers/{id}', [ManagerController::class, 'show']);
    Route::post('/managers', [ManagerController::class, 'store']);
    Route::put('/managers/{id}', [ManagerController::class, 'update']);
    Route::delete('/managers/{id}', [ManagerController::class, 'destroy']);
    Route::post('/managers/{id}/reset-credential', [ManagerController::class, 'resetCredential']);
    Route::get('/managers/{id}/new-hires', [ManagerController::class, 'newHires']);

    // Manager Portal
    Route::get('/manager/new-hires', [ManagerController::class, 'myNewHires']);
    Route::get('/manager/new-hires/{employeeId}', [ManagerController::class, 'myNewHireDetail']);
    Route::get('/manager/new-hires/{employeeId}/planning-history', [ManagerController::class, 'myNewHirePlanningHistory']);
    Route::get('/manager/approval-requests', [ManagerController::class, 'myApprovalRequests']);
    Route::get('/manager/approval-summary', [ManagerController::class, 'myApprovalSummary']);
});
