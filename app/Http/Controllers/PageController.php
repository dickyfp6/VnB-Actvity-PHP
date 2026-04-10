<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

class PageController extends Controller
{
    public function dashboard() 
    { 
        $user = auth()->user();
        
        // Redirect based on user role
        if ($user->hasRole('pcx_manager') || $user->hasRole('intercomm')) {
            return Redirect::route('dashboard.pcx');
        }
        
        if ($user->hasRole('manager')) {
            // Akan membuat manager dashboard nanti
            // return Redirect::route('dashboard.manager');
        }
        
        if ($user->hasRole('employee')) {
            // Akan membuat employee dashboard nanti
            // return Redirect::route('dashboard.employee');
        }
        
        // Default fallback
        return view('dashboard');
    }
    public function employees() { return view('employees.index'); }
    public function vnbPlans() { return view('vnb-plans.index'); }
    public function evidence() { return view('evidence.index'); }
    public function intercomm() 
    { 
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengelola Intercomm');
        return view('intercomm.index'); 
    }
    public function vnbFramework() { return view('vnb-framework.index'); }
    public function managers() 
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'intercomm', 'pcx_manager']), 403, 'Anda tidak memiliki akses ke Manage Manager');
        return view('managers.index');
    }
    public function vnbActivity() { return view('vnb-activity.index'); }
    public function reviewActivity() { return view('review-activity.index'); }
    public function masterData() { return view('master-data.index'); }
    public function managerEmployees()
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Manager Employee');
        return view('manager-employees.index');
    }
    public function managerEmployeeDetail(int $employeeId)
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Detail Employee Manager');
        return view('manager-employees.detail', compact('employeeId'));
    }
    public function managerPlanningHistory(int $employeeId)
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Planning History');
        return view('manager-employees.planning-history', compact('employeeId'));
    }
    public function managerApprovalRequests()
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Approval Request Manager');
        return view('manager-approval.index');
    }
    public function managerApprovalDetail(int $planId)
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Approval Request Manager');
        return view('manager-approval.detail', compact('planId'));
    }
    public function profile()
    {
        abort_unless(auth()->check(), 403, 'Anda harus login.');
        $user = auth()->user();
        $employee = $user->employee;
        $manager = $user->manager; // untuk manager role
        $role = $user->getRoleNames()->first();
        return view('account.profile', compact('user', 'employee', 'manager', 'role'));
    }

    public function changePassword()
    {
        abort_unless(auth()->check(), 403, 'Anda harus login.');
        return view('account.change-password');
    }
}