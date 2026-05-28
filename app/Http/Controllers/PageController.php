<?php

namespace App\Http\Controllers;

use App\Models\VnbActivityAssignment;
use App\Support\ActiveRoleContext;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard(Request $request)
    { 
        ActiveRoleContext::current($request, auth()->user());

        return view('dashboard');
    }

    public function star(Request $request)
    {
        $this->ensureActiveRole($request, ['employee', 'manager', 'intercomm', 'pcx_manager', 'direktur_utama']);
        return view('star.index');
    }

    public function employees(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager', 'manager']);
        return view('employees.index');
    }

    public function employeeDetail(Request $request, int $employeeId)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager', 'manager']);

        $query = http_build_query([
            'manager_id' => $request->query('manager_id'),
        ]);

        $backUrl = '/employees' . ($query ? ('?' . $query) : '');

        return view('employees.detail', compact('employeeId', 'backUrl'));
    }

    public function vnbPlans(Request $request)
    {
        $this->ensureActiveRole($request, ['employee']);
        return view('vnb-plans.index');
    }

    public function evidence() { return view('evidence.index'); }

    public function intercomm(Request $request)
    { 
        $this->ensureActiveRole($request, ['pcx_manager']);
        return view('intercomm.index'); 
    }

    public function vnbFramework(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager']);
        return view('vnb-framework.index');
    }

    public function managers(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager']);
        return view('managers.index');
    }

    public function vnbActivity(Request $request)
    {
        $user = auth()->user();
        $activeRole = ActiveRoleContext::current($request, $user);

        abort_unless(in_array($activeRole, ['employee', 'intercomm', 'pcx_manager', 'manager'], true), 403, 'Anda tidak memiliki akses ke VnB Activity.');

        if ($activeRole === 'employee') {
            $isAssigned = VnbActivityAssignment::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->exists();

            if (!$isAssigned) {
                return view('vnb-activity.not-assigned');
            }
        }

        return view('vnb-activity.index');
    }

    public function reviewActivity(Request $request)
    {
        $this->ensureActiveRole($request, ['manager', 'intercomm', 'pcx_manager']);
        return view('review-activity.index');
    }

    public function masterData(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager']);
        return view('master-data.index');
    }

    public function managerApprovalRequests(Request $request)
    {
        $this->ensureActiveRole($request, ['manager']);
        return view('manager-approval.index');
    }
    public function planFeedback(int $planId)
    {
        // Let API handle auth - just return view
        return view('plan-feedback.index', compact('planId'));
    }
    public function profile(Request $request)
    {
        abort_unless(auth()->check(), 403, 'Anda harus login.');
        $user = auth()->user();
        $employee = $user->employee;
        $manager = $user->manager; // untuk manager role
        $role = ActiveRoleContext::current($request, $user);
        return view('account.profile', compact('user', 'employee', 'manager', 'role'));
    }

    public function changePassword()
    {
        abort_unless(auth()->check(), 403, 'Anda harus login.');
        return view('account.change-password');
    }

    // ==================== Sinkronisasi Data Routes ====================
    public function hris(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager']);
        return view('hris.index');
    }

    // ==================== STAR Routes ====================
    public function starSchema(Request $request)
    {
        $this->ensureActiveRole($request, ['employee', 'manager', 'intercomm', 'pcx_manager', 'direktur_utama']);
        return view('star.schema');
    }

    public function starRecognition(Request $request)
    {
        $this->ensureActiveRole($request, ['employee', 'manager', 'intercomm', 'pcx_manager', 'direktur_utama']);
        return view('star.recognition');
    }

    public function starRecognitionCreate(Request $request)
    {
        $this->ensureActiveRole($request, ['employee', 'manager', 'intercomm', 'pcx_manager', 'direktur_utama']);
        return view('star.create');
    }

    public function starAchievements(Request $request)
    {
        $this->ensureActiveRole($request, ['employee', 'manager', 'intercomm', 'pcx_manager', 'direktur_utama']);
        return view('star.achievements');
    }

    public function starApproval(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager', 'direktur_utama']);
        return view('star.approval');
    }

    public function starApprovalReview(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager', 'direktur_utama']);

        return view('star.approval-review', [
            'group' => $request->query('group'),
            'reviewId' => $request->query('reviewId'),
        ]);
    }

    // ==================== VNB Routes ====================
    public function vnbParticipants(Request $request)
    {
        $this->ensureActiveRole($request, ['intercomm', 'pcx_manager']);
        return view('vnb-participants.index');
    }

    public function vnbApproval(Request $request)
    {
        $this->ensureActiveRole($request, ['manager']);
        return view('manager-approval.index');
    }

    private function ensureActiveRole(Request $request, array $allowedRoles): void
    {
        $user = auth()->user();
        abort_unless(
            ActiveRoleContext::hasActiveRole($request, $user, $allowedRoles),
            403,
            'Halaman ini tidak tersedia untuk role aktif Anda.'
        );
    }
}