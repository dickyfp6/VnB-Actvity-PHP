<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\PcxDashboardController;

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
    Route::post('/switch-role', [AuthWebController::class, 'switchRole'])->name('switch-role');

    // ==================== DASHBOARD ====================
    Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard.alt');
    
    // ==================== ROOT LEVEL ROUTES ====================
    // /sinkronisasi - PCX, Intercomm
    Route::get('/sinkronisasi', [PageController::class, 'hris'])->name('hris');
    
    // /employees - PCX, Intercomm, Manager
    Route::get('/employees', [PageController::class, 'employees'])->name('employees');
    
    // /intercomm - PCX
    Route::get('/intercomm', [PageController::class, 'intercomm'])->name('intercomm');
    
    // /managers - PCX, Intercomm
    Route::get('/managers', [PageController::class, 'managers'])->name('managers');
    
    // /master-data - PCX, Intercomm
    Route::get('/master-data', [PageController::class, 'masterData'])->name('master-data');

    // ==================== STAR ====================
    Route::prefix('star')->group(function () {
        // /schema - Semua Role
        Route::get('/schema', [PageController::class, 'starSchema'])->name('star.schema');
        
        // /recognition - Semua Role
        Route::get('/recognition', [PageController::class, 'starRecognition'])->name('star.recognition');
        
        // /achievements - Semua Role
        Route::get('/achievements', [PageController::class, 'starAchievements'])->name('star.achievements');
        
        // /star-approval - PCX, Intercomm, Direktur Utama
        Route::get('/star-approval', [PageController::class, 'starApproval'])->name('star.star-approval');
    });

    // ==================== VNB ACTIVITY ====================
    Route::prefix('vnb')->group(function () {
        // /framework - PCX, Intercomm
        Route::get('/framework', [PageController::class, 'vnbFramework'])->name('vnb.framework');
        
        // /plan - Employee
        Route::get('/plan', [PageController::class, 'vnbPlans'])->name('vnb.plan');
        Route::get('/plan/{planId}/feedback', [PageController::class, 'planFeedback'])->name('vnb.plan.feedback');
        
        // /activity - Employee
        Route::get('/activity', [PageController::class, 'vnbActivity'])->name('vnb.activity');
        
        // /participants - PCX, Intercomm
        Route::get('/participants', [PageController::class, 'vnbParticipants'])->name('vnb.participants');
        
        // /vnb-approval - Manager
        Route::get('/vnb-approval', [PageController::class, 'vnbApproval'])->name('vnb.vnb-approval');
    });

    // ==================== PROFILE (Semua Employee) ====================
    Route::prefix('profile')->group(function () {
        // /details
        Route::get('/details', [PageController::class, 'profile'])->name('profile.details');
        
        // /change-password
        Route::get('/change-password', [PageController::class, 'changePassword'])->name('profile.change-password');
    });

    // Legacy aliases for backward compatibility
    Route::get('/evidence', [PageController::class, 'evidence'])->name('evidence');
    Route::get('/star', [PageController::class, 'starSchema'])->name('star');
    Route::get('/vnb-plans', [PageController::class, 'vnbPlans'])->name('vnb-plans');
    Route::get('/vnb-activity', [PageController::class, 'vnbActivity'])->name('vnb-activity');
    Route::get('/review-activity', [PageController::class, 'reviewActivity'])->name('review-activity');
    Route::get('/manager/employees', [PageController::class, 'managerEmployees'])->name('manager.employees');
    Route::get('/manager/employees/{employeeId}', [PageController::class, 'managerEmployeeDetail'])->name('manager.employee.detail');
    Route::get('/manager/employees/{employeeId}/planning-history', [PageController::class, 'managerPlanningHistory'])->name('manager.employee.planning-history');
    Route::redirect('/manager/approval-requests', '/vnb/vnb-approval')->name('manager.approval-requests');
});
