<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'phone' => 'nullable|string',
            'role' => 'required|in:new_hire,manager,pcx_manager,intercomm,admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ]
        ], 201);
    }

    /**
     * Login user and get token
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $credential = trim((string) $validated['email']);

        $users = User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($credential)])
            ->orWhereHas('employee', function ($query) use ($credential) {
                $query->where('employee_number', $credential);
            })
            ->orWhereHas('manager', function ($query) use ($credential) {
                $query->where('employee_number', $credential);
            })
            ->get();

        $user = $users->first(function (User $candidate) use ($validated) {
            return Hash::check($validated['password'], $candidate->password);
        });

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are invalid.'],
            ]);
        }

        $blockedReason = $this->resolveNewHireAccessBlockReason($user);
        if ($blockedReason !== null) {
            $this->deactivateUserAccount($user);

            return response()->json([
                'success' => false,
                'message' => $blockedReason,
            ], 403);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User logged in successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
            ],
            'token' => $token
        ]);
    }

    private function resolveNewHireAccessBlockReason(User $user): ?string
    {
        if (!$user->isNewHire()) {
            return null;
        }

        if (!$user->employee_id) {
            return 'Akun New Hire tidak valid.';
        }

        $employee = Employee::withTrashed()->find($user->employee_id);
        if (!$employee || $employee->trashed()) {
            return 'Akun tidak dapat digunakan karena data New Hire sudah dihapus.';
        }

        $employeeStatus = Str::lower(trim((string) ($employee->employee_status ?? '')));
        $statusChangeReason = Str::lower(trim((string) ($employee->status_change_reason ?? '')));
        if (Str::contains($employeeStatus, 'mutasi') || Str::contains($statusChangeReason, 'mutasi')) {
            return 'Akun tidak dapat digunakan karena New Hire sudah dimutasi.';
        }

        $employmentState = Str::lower(trim((string) ($employee->employment_state ?? 'active')));
        if (in_array($employmentState, ['resigned', 'terminated'], true)) {
            return 'Akun tidak dapat digunakan karena status New Hire tidak aktif.';
        }

        if ($employmentState === 'graduated') {
            $referenceTime = $employee->status_changed_at
                ?? $employee->vnb_period_end
                ?? $employee->updated_at;

            if ($referenceTime instanceof Carbon && now()->greaterThan($referenceTime->copy()->addDays(30))) {
                return 'Akun tidak dapat digunakan karena New Hire sudah lulus lebih dari 30 hari.';
            }
        }

        return null;
    }

    private function deactivateUserAccount(User $user): void
    {
        if ($user->status !== 'inactive') {
            $user->update(['status' => 'inactive']);
        }

        $user->tokens()->delete();
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
            ]
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully'
        ]);
    }

    /**
     * Verify current password
     */
    public function verifyPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
        ]);

        $user = $request->user();

        $isValid = Hash::check($validated['current_password'], $user->password);

        return response()->json([
            'success' => true,
            'valid' => $isValid,
            'message' => $isValid ? 'Password saat ini sesuai.' : 'Password saat ini tidak sesuai.',
        ]);
    }

    /**
     * Change password for authenticated user
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed|different:current_password',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak sesuai.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.',
        ]);
    }
}
