<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class IntercommController extends Controller
{
    private const REQUIRED_DIVISION = 'Human Resource';
    private const REQUIRED_DEPARTMENT = 'People, Culture, and Experience';

    /**
     * UC001: List intercomm users
     * Hanya PCX Manager yang dapat mengakses
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengelola Intercomm');
        $query = User::role('intercomm')->with('employee');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhereHas('employee', function ($employeeQuery) use ($request) {
                      $employeeQuery->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('employee_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rows = $query
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'status', 'employee_id', 'created_at'])
            ->map(function (User $user): array {
                $employee = $user->employee;

                return [
                    'id' => $user->id,
                    'employee_id' => $employee?->id,
                    'employee_number' => $employee?->employee_number,
                    'name' => $employee?->name ?? $user->name,
                    'date_joined' => optional($employee?->date_joined)->toDateString(),
                    'email' => $employee?->email ?? $user->email,
                    'level' => $employee?->level,
                    'employee_status' => $employee?->employee_status,
                    'status' => $user->status,
                    'assigned_at' => optional($user->created_at)->toDateTimeString(),
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
                $q->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(self::REQUIRED_DIVISION)]);
            })
            ->whereHas('department', function ($q) {
                $q->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(self::REQUIRED_DEPARTMENT)]);
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
     * UC001 Scenario A: Add Intercomm - create user account with random 6-digit password
     * Hanya PCX Manager yang dapat menambah Intercomm
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

        $isValidDivision = mb_strtolower(trim((string) ($employee->division?->name ?? ''))) === mb_strtolower(self::REQUIRED_DIVISION);
        $isValidDepartment = mb_strtolower(trim((string) ($employee->department?->name ?? ''))) === mb_strtolower(self::REQUIRED_DEPARTMENT);
        if (!$isValidDivision || !$isValidDepartment) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak memenuhi syarat assignment Intercomm (Human Resource / People, Culture, and Experience).',
            ], 422);
        }

        $existingIntercomm = User::role('intercomm')->where('employee_id', $employee->id)->exists();
        if ($existingIntercomm) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ini sudah terdaftar sebagai Intercomm.',
            ], 422);
        }

        $rawPassword = null;
        $user = $employee->user;

        if ($user) {
            $user->update([
                'name' => $employee->name,
                'email' => $employee->email,
                'status' => 'active',
                'employee_id' => $employee->id,
            ]);
        } else {
            if (empty($employee->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee tidak memiliki email, sehingga akun Intercomm tidak dapat dibuat.',
                ], 422);
            }

            $rawPassword = (string) random_int(100000, 999999);

            $user = User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => Hash::make($rawPassword),
                'status' => 'active',
                'employee_id' => $employee->id,
                'email_verified_at' => now(),
            ]);
        }

        if (!$user->hasRole('intercomm')) {
            $user->assignRole('intercomm');
        }

        return response()->json([
            'success' => true,
            'message' => $rawPassword
                ? 'Intercomm berhasil di-assign. Akun baru dibuat untuk employee.'
                : 'Intercomm berhasil di-assign ke akun employee yang sudah ada.',
            'data' => $user->only('id', 'name', 'email', 'status', 'employee_id'),
            'temp_password' => $rawPassword,
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
        $user = User::role('intercomm')->findOrFail($id);
        $user->update(['status' => 'inactive']);

        return response()->json(['success' => true, 'message' => 'Akun Intercomm berhasil dinonaktifkan']);
    }

    /**
     * Reactivate intercomm
     * Hanya PCX Manager yang dapat mengaktifkan kembali Intercomm
     */
    public function activate(int $id): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengaktifkan Intercomm');
        $user = User::role('intercomm')->findOrFail($id);
        $user->update(['status' => 'active']);

        return response()->json(['success' => true, 'message' => 'Akun Intercomm berhasil diaktifkan']);
    }
}
