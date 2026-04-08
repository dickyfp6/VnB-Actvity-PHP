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

    Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [PageController::class, 'employees'])->name('employees');
    Route::get('/vnb-plans', [PageController::class, 'vnbPlans'])->name('vnb-plans');
    Route::get('/evidence', [PageController::class, 'evidence'])->name('evidence');

    // ==================== DASHBOARD ROUTES ====================
    // PCX & Intercomm Dashboard
    Route::get('/dashboard/pcx', [PcxDashboardController::class, 'index'])->name('dashboard.pcx');
    
    // Manager Dashboard (akan ditambahkan nanti)
    // Route::get('/dashboard/manager', [ManagerDashboardController::class, 'index'])->name('dashboard.manager');
    
    // New Hire Dashboard (akan ditambahkan nanti)
    // Route::get('/dashboard/new-hire', [NewHireDashboardController::class, 'index'])->name('dashboard.new-hire');

    // ==================== END DASHBOARD ROUTES ====================

    // UC001 - Manage Intercomm
    Route::get('/intercomm', [PageController::class, 'intercomm'])->name('intercomm');

    // UC002 - Manage V&B Framework
    Route::get('/vnb-framework', [PageController::class, 'vnbFramework'])->name('vnb-framework');

    // UC003 - Manage Manager
    Route::get('/managers', [PageController::class, 'managers'])->name('managers');

    // UC006 - VnB Activity
    Route::get('/vnb-activity', [PageController::class, 'vnbActivity'])->name('vnb-activity');

    // UC007 - Review & Approve Activity
    Route::get('/review-activity', [PageController::class, 'reviewActivity'])->name('review-activity');

    // Manager Portal
    Route::get('/manager/new-hires', [PageController::class, 'managerNewHires'])->name('manager.new-hires');
    Route::get('/manager/new-hires/{employeeId}', [PageController::class, 'managerNewHireDetail'])->name('manager.new-hire.detail');
    Route::get('/manager/new-hires/{employeeId}/planning-history', [PageController::class, 'managerPlanningHistory'])->name('manager.new-hire.planning-history');
    Route::get('/manager/approval-requests', [PageController::class, 'managerApprovalRequests'])->name('manager.approval-requests');
    Route::get('/manager/approval/{planId}', [PageController::class, 'managerApprovalDetail'])->name('manager.approval.detail');

    // UC009 - Master Database
    Route::get('/master-data', [PageController::class, 'masterData'])->name('master-data');

    // Account - Profile (dengan profile info + password change dalam satu halaman)
    Route::get('/my-account/profile', [PageController::class, 'profile'])->name('my-account.profile');
    Route::get('/my-account/change-password', [PageController::class, 'changePassword'])->name('my-account.change-password');
});
