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
    Route::get('/plan-feedback/{planId}', [PageController::class, 'planFeedback'])->name('plan.feedback');
    Route::get('/evidence', [PageController::class, 'evidence'])->name('evidence');

    // ==================== DASHBOARD ROUTES ====================
    // PCX & Intercomm Dashboard
    Route::get('/dashboard/pcx', [PcxDashboardController::class, 'index'])->name('dashboard.pcx');
    
    // Manager Dashboard (akan ditambahkan nanti)
    // Route::get('/dashboard/manager', [ManagerDashboardController::class, 'index'])->name('dashboard.manager');
    
    // Employee Dashboard (akan ditambahkan nanti)
    // Route::get('/dashboard/employee', [EmployeeDashboardController::class, 'index'])->name('dashboard.employee');

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
    Route::get('/manager/employees', [PageController::class, 'managerEmployees'])->name('manager.employees');
    Route::get('/manager/employees/{employeeId}', [PageController::class, 'managerEmployeeDetail'])->name('manager.employee.detail');
    Route::get('/manager/employees/{employeeId}/planning-history', [PageController::class, 'managerPlanningHistory'])->name('manager.employee.planning-history');
    Route::get('/manager/approval-requests', [PageController::class, 'managerApprovalRequests'])->name('manager.approval-requests');

    // UC009 - Master Database
    Route::get('/master-data', [PageController::class, 'masterData'])->name('master-data');

    // Account - Profile (dengan profile info + password change dalam satu halaman)
    Route::get('/my-account/profile', [PageController::class, 'profile'])->name('my-account.profile');
    Route::get('/my-account/change-password', [PageController::class, 'changePassword'])->name('my-account.change-password');
});
