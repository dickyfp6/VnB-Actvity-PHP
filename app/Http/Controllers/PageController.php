<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function dashboard() { return view('dashboard'); }
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
    public function managerNewHires()
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Manager New Hire');
        return view('manager-new-hires.index');
    }
    public function managerNewHireDetail(int $employeeId)
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Detail New Hire Manager');
        return view('manager-new-hires.detail', compact('employeeId'));
    }
    public function managerPlanningHistory(int $employeeId)
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Planning History');
        return view('manager-new-hires.planning-history', compact('employeeId'));
    }
    public function managerApprovalRequests()
    {
        abort_unless(auth()->user()->hasAnyRole(['manager', 'admin']), 403, 'Anda tidak memiliki akses ke Approval Request Manager');
        return view('manager-approval.index');
    }
    public function profile()
    {
        abort_unless(auth()->check(), 403, 'Anda harus login.');
        $user = auth()->user();
        $employee = $user->employee;
        return view('account.profile', compact('user', 'employee'));
    }
}