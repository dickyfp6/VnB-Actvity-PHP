<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Traits\HandlesUserProvisioning;

class IntercommController extends Controller
{
    use HandlesUserProvisioning;

    private const REQUIRED_DIVISIONS = [
        'Human Resource',
        'Human Resources',
        'HR',
    ];

    private const REQUIRED_DEPARTMENTS = [
        'People, Culture, and Experience',
        'People, Culture, and Experiences',
    ];

    /**
     * UC001: List eligible employees and their Intercomm role status
     * Hanya PCX Manager yang dapat mengakses
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengelola Intercomm');

        $query = Employee::query()
            ->with(['division', 'department', 'position', 'user.roles'])
            ->where('status', 'Aktif')
            ->whereHas('division', function ($q) {
                $q->whereRaw(
                    'LOWER(TRIM(name)) IN (' . implode(',', array_fill(0, count(self::REQUIRED_DIVISIONS), '?')) . ')',
                    array_map(fn (string $value) => mb_strtolower(trim($value)), self::REQUIRED_DIVISIONS)
                );
            })
            ->whereHas('department', function ($q) {
                $q->whereRaw(
                    'LOWER(TRIM(name)) IN (' . implode(',', array_fill(0, count(self::REQUIRED_DEPARTMENTS), '?')) . ')',
                    array_map(fn (string $value) => mb_strtolower(trim($value)), self::REQUIRED_DEPARTMENTS)
                );
            });

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('employee_status', $request->status);
        }

        $rows = $query
            ->orderBy('name')
            ->get()
            ->map(function (Employee $employee): array {
                $user = $employee->user;
                $hasIntercommRole = (bool) ($user?->roles?->contains('name', 'intercomm'));

                return [
                    'id' => $employee->id,
                    'employee_id' => $employee->id,
                    'employee_number' => $employee->employee_number,
                    'name' => $employee->name,
                    'date_joined' => optional($employee->date_joined)->toDateString(),
                    'email' => $employee->email,
                    'division' => $employee->division?->name,
                    'department' => $employee->department?->name,
                    'position' => $employee->position?->name,
                    'level' => $employee->level,
                    'employee_status' => $employee->employee_status,
                    'is_intercomm' => $hasIntercommRole,
                    'user_id' => $user?->id,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Get employee options for intercomm assignment.
     * Only employees from Human Resource / People, Culture, and Experience are eligible.
     */
    public function employeeOptions(): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengelola Intercomm');

        $alreadyAssignedIds = User::role('intercomm')
            ->whereNotNull('employee_id')
            ->pluck('employee_id')
            ->all();

        $rows = Employee::query()
            ->with(['division', 'department'])
            ->where('status', 'Aktif')
            ->whereHas('division', function ($q) {
                $q->whereRaw(
                    'LOWER(TRIM(name)) IN (' . implode(',', array_fill(0, count(self::REQUIRED_DIVISIONS), '?')) . ')',
                    array_map(fn (string $value) => mb_strtolower(trim($value)), self::REQUIRED_DIVISIONS)
                );
            })
            ->whereHas('department', function ($q) {
                $q->whereRaw(
                    'LOWER(TRIM(name)) IN (' . implode(',', array_fill(0, count(self::REQUIRED_DEPARTMENTS), '?')) . ')',
                    array_map(fn (string $value) => mb_strtolower(trim($value)), self::REQUIRED_DEPARTMENTS)
                );
            })
            ->when(!empty($alreadyAssignedIds), fn ($q) => $q->whereNotIn('id', $alreadyAssignedIds))
            ->orderBy('name')
            ->get()
            ->map(function (Employee $employee): array {
                return [
                    'id' => $employee->id,
                    'employee_number' => $employee->employee_number,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'date_joined' => optional($employee->date_joined)->toDateString(),
                    'division' => $employee->division?->name,
                    'department' => $employee->department?->name,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * UC001 Scenario A: Add Intercomm - assign role to existing employee account
     * Hanya PCX Manager yang dapat menambah Intercomm
     * Employee sudah punya akun dari sistem HRIS/HRMS
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat menambah Intercomm');
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        $employee = Employee::query()
            ->with(['division', 'department', 'user'])
            ->findOrFail((int) $validated['employee_id']);

        $divisionName = mb_strtolower(trim((string) ($employee->division?->name ?? '')));
        $departmentName = mb_strtolower(trim((string) ($employee->department?->name ?? '')));
        $isValidDivision = in_array($divisionName, array_map(fn (string $value) => mb_strtolower(trim($value)), self::REQUIRED_DIVISIONS), true);
        $isValidDepartment = in_array($departmentName, array_map(fn (string $value) => mb_strtolower(trim($value)), self::REQUIRED_DEPARTMENTS), true);
        
        if (!$isValidDivision || !$isValidDepartment) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak memenuhi syarat assignment Intercomm (Human Resource + PCX).',
            ], 422);
        }

        if ($this->isEmployeeIntercomm($employee)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ini sudah terdaftar sebagai Intercomm.',
            ], 422);
        }

        $user = $employee->user;
        $tempPassword = null;

        if (!$user) {
            $tempPassword = $this->provisionEmployeeUserAccount($employee);
            $user = $employee->fresh()->user;
        }

        if ($user) {
            $user->assignRole('intercomm');
        }

        return response()->json([
            'success' => true,
            'message' => 'Intercomm berhasil di-assign ke akun employee.',
            'temp_password' => $tempPassword,
            'data' => $employee->fresh(['division', 'department', 'user.roles'])->toArray(),
        ], 201);
    }

    /**
     * UC001 Scenario B: Update intercomm info
     * Hanya PCX Manager yang dapat mengubah data Intercomm
     */
    public function update(Request $request, int $id): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengubah data Intercomm');
        $user = User::role('intercomm')->findOrFail($id);

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
        ]);

        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'Data Intercomm berhasil diperbarui', 'data' => $user]);
    }

    /**
     * UC001 Scenario C: Deactivate intercomm
     * Hanya PCX Manager yang dapat menonaktifkan Intercomm
     */
    public function deactivate(int $id): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat menonaktifkan Intercomm');
        $employee = Employee::query()->with('user.roles')->findOrFail($id);
        $user = $employee->user;

        if (!$user || !$user->hasRole('intercomm')) {
            return response()->json(['success' => true, 'message' => 'Intercomm sudah nonaktif.']);
        }

        $user->removeRole('intercomm');

        return response()->json(['success' => true, 'message' => 'Role Intercomm berhasil dicabut.']);
    }

    /**
     * Reactivate intercomm
     * Hanya PCX Manager yang dapat mengaktifkan kembali Intercomm
     */
    public function activate(int $id): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengaktifkan Intercomm');
        $employee = Employee::query()->with(['division', 'department', 'user.roles'])->findOrFail($id);

        $user = $employee->user;
        $tempPassword = null;

        if (!$user) {
            $tempPassword = $this->provisionEmployeeUserAccount($employee);
            $user = $employee->fresh()->user;
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengaktifkan: Akun user tidak dapat dibuat.',
            ], 422);
        }

        if (!$user->hasRole('intercomm')) {
            $user->assignRole('intercomm');
        }

        return response()->json([
            'success' => true,
            'message' => 'Role Intercomm berhasil diaktifkan.',
            'temp_password' => $tempPassword,
            'data' => $employee->fresh(['division', 'department', 'user.roles'])->toArray(),
        ]);
    }

    private function isEmployeeIntercomm(Employee $employee): bool
    {
        return (bool) ($employee->user?->roles?->contains('name', 'intercomm'));
    }
}
