<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthWebController;

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
    Route::post('/switch-role', [AuthWebController::class, 'switchRole'])->name('switch-role');

    // ==================== BERANDA (127.0.0.1:8000) ====================
    Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard.alt');
    
    Route::get('/hris', [PageController::class, 'hris'])->name('hris');
    Route::get('/employees', [PageController::class, 'employees'])->name('employees');
    Route::get('/intercomm', [PageController::class, 'intercomm'])->name('intercomm');
    Route::get('/managers', [PageController::class, 'managers'])->name('managers');
    Route::get('/master-data', [PageController::class, 'masterData'])->name('master-data');

    // ==================== STAR ====================
    Route::prefix('star')->name('star.')->group(function () {
        Route::get('/schema', [PageController::class, 'starSchema'])->name('schema');
        Route::get('/recognition', [PageController::class, 'starRecognition'])->name('recognition');
        Route::get('/achievements', [PageController::class, 'starAchievements'])->name('achievements');
        Route::get('/approval', [PageController::class, 'starApproval'])->name('approval');
    });

    // ==================== VNB ACTIVITY ====================
    Route::prefix('vnb')->name('vnb.')->group(function () {
        Route::get('/framework', [PageController::class, 'vnbFramework'])->name('framework');
        Route::get('/plans', [PageController::class, 'vnbPlans'])->name('plans');
        Route::get('/activity', [PageController::class, 'vnbActivity'])->name('activity');
        Route::get('/participants', [PageController::class, 'vnbParticipants'])->name('participants');
        Route::get('/approval', [PageController::class, 'vnbApproval'])->name('approval');
    });

    // ==================== PROFILE (All Employees) ====================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [PageController::class, 'profile'])->name('index');
        Route::get('/change-password', [PageController::class, 'changePassword'])->name('change-password');
    });

    // ==================== LEGACY / MANAGER PORTAL ====================
    // Manager Portal Routes
    Route::get('/manager/employees', [PageController::class, 'managerEmployees'])->name('manager.employees');
    Route::get('/manager/employees/{employeeId}', [PageController::class, 'managerEmployeeDetail'])->name('manager.employee.detail');
    Route::get('/manager/employees/{employeeId}/planning-history', [PageController::class, 'managerPlanningHistory'])->name('manager.employee.planning-history');
    Route::get('/manager/approval-requests', [PageController::class, 'managerApprovalRequests'])->name('manager.approval-requests');

    // Legacy routes (for backward compatibility with API/other references)
    Route::get('/plan-feedback/{planId}', [PageController::class, 'planFeedback'])->name('plan.feedback');
    Route::get('/evidence', [PageController::class, 'evidence'])->name('evidence');
    Route::get('/vnb-plans', [PageController::class, 'vnbPlans'])->name('vnb-plans');
});
