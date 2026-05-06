<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ActiveRoleContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthWebController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoGroups = $this->buildDemoLoginGroups();

        return view('auth.login', compact('demoGroups'));
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string',
            'password' => 'required',
        ]);

        $credential = Str::upper(trim((string) $validated['nip']));

        $users = User::query()
            ->whereRaw('UPPER(email) = ?', [$credential])
            ->orWhereRaw('UPPER(employee_number) = ?', [$credential])
            ->orWhereHas('employee', function ($query) use ($credential) {
                $query->whereRaw('UPPER(employee_number) = ?', [$credential]);
            })
            ->orWhereHas('manager', function ($query) use ($credential) {
                $query->whereRaw('UPPER(employee_number) = ?', [$credential]);
            })
            ->get();

        $user = $users->first(function (User $candidate) use ($validated) {
            return Hash::check($validated['password'], $candidate->password);
        });

        if (!$user) {
            throw ValidationException::withMessages([
                'nip' => ['The provided credentials are invalid.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'nip' => ['User account is inactive.'],
            ]);
        }

        Auth::login($user, remember: $request->boolean('remember'));
        $request->session()->regenerate();
        ActiveRoleContext::resolve($request, $user);

        return redirect()->route('dashboard');
    }

    /**
     * Show register page
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|in:employee,manager,pcx_manager,intercomm,direktur_utama',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $user->assignRole($validated['role']);
        $user->forceFill(['last_active_role' => $validated['role']])->save();

        Auth::login($user);
        $request->session()->regenerate();
        ActiveRoleContext::resolve($request, $user);

        return redirect()->route('dashboard');
    }

    /**
     * Switch active role for multi-role users.
     */
    public function switchRole(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string',
        ]);

        $user = Auth::user();
        ActiveRoleContext::switch($request, $user, $validated['role']);

        return redirect()->route('dashboard')->with('success', 'Role aktif berhasil diganti.');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $activeRole = $request->session()->get(ActiveRoleContext::SESSION_KEY);
            if (is_string($activeRole) && $activeRole !== '') {
                $user->forceFill(['last_active_role' => $activeRole])->save();
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function buildDemoLoginGroups(): array
    {
        $users = User::query()
            ->with(['roles', 'employee.division'])
            ->whereNotNull('employee_number')
            ->where('status', 'active')
            ->orderBy('employee_number')
            ->get();

        if ($users->isEmpty()) {
            return [];
        }

        $primaryEmployeeNumbers = [
            'EMP1001',
            'EMP1002',
            'EMP1003',
            'EMP1004',
            'EMP1005',
            'EMP1006',
        ];

        $primaryGroup = [
            'key' => 'utama',
            'label' => 'Demo Utama',
            'description' => 'Akun seeded utama untuk login cepat.',
            'tone' => 'emerald',
            'accounts' => [],
        ];

        $secondaryUsers = $users->reject(function (User $user) use ($primaryEmployeeNumbers) {
            return in_array($user->employee_number, $primaryEmployeeNumbers, true);
        });

        $primaryUsers = $users->filter(function (User $user) use ($primaryEmployeeNumbers) {
            return in_array($user->employee_number, $primaryEmployeeNumbers, true);
        });

        // Sort primary users dengan Developer paling atas
        $primaryUsers = $primaryUsers->sortBy(function (User $user): int {
            $priority = [
                'EMP1006' => 0,  // Developer paling atas
                'EMP1001' => 1,  // Direktur Utama
                'EMP1002' => 2,  // PCX Manager
                'EMP1003' => 3,  // Intercomm
                'EMP1004' => 4,  // Manager
                'EMP1005' => 5,  // Employee
            ];
            return $priority[$user->employee_number] ?? 999;
        })->values();

        foreach ($primaryUsers as $user) {
            $primaryGroup['accounts'][] = $this->buildDemoCredentialPayload($user, true);
        }

        $roleGroups = $secondaryUsers
            ->groupBy(fn (User $user) => $this->resolveDemoRoleKey($user))
            ->map(function ($group, string $roleKey): array {
                $orderedUsers = $group->sortBy(function (User $user): array {
                    $divisionRank = $this->resolveDivisionRank($user->employee?->division?->name);
                    return [$divisionRank, $user->name];
                })->values();

                $payloads = $orderedUsers->map(fn (User $user) => $this->buildDemoCredentialPayload($user))->all();

                return [
                    'key' => $roleKey,
                    'label' => $this->resolveDemoRoleLabel($roleKey),
                    'description' => $this->resolveDemoRoleDescription($roleKey),
                    'tone' => $this->resolveDemoTone($roleKey),
                    'accounts' => $payloads,
                ];
            })
            ->values()
            ->all();

        $groups = array_merge([$primaryGroup], $roleGroups);

        return array_values(array_filter($groups, fn (array $group): bool => !empty($group['accounts'])));
    }

    private function buildDemoCredentialPayload($user, bool $forceDefaultPassword = false): array
    {
        $employeeNumber = trim((string) $user->employee_number);
        $employeeName = trim((string) ($user->name ?? ''));
        $divisionName = trim((string) ($user->employee?->division?->name ?? ''));
        $roleKey = $this->resolveDemoRoleKey($user);

        return [
            'id' => $user->id,
            'name' => $employeeName !== '' ? $employeeName : $employeeNumber,
            'nip' => $employeeNumber,
            'password' => $forceDefaultPassword ? 'password' : $this->buildDefaultDemoPassword($employeeName, $employeeNumber),
            'role_key' => $roleKey,
            'role_label' => $this->resolveDemoRoleLabel($roleKey),
            'division' => $divisionName !== '' ? $divisionName : '-',
            'division_tone' => $this->resolveDivisionTone($divisionName),
            'division_badge_class' => $this->resolveDivisionBadgeClass($divisionName),
        ];
    }

    private function resolveDemoRoleKey(User $user): string
    {
        $roleNames = $user->roles->pluck('name')->all();
        $priority = ['direktur_utama', 'pcx_manager', 'intercomm', 'manager', 'employee'];

        foreach ($priority as $roleKey) {
            if (in_array($roleKey, $roleNames, true)) {
                return $roleKey;
            }
        }

        return 'employee';
    }

    private function resolveDemoRoleLabel(string $roleKey): string
    {
        return match ($roleKey) {
            'direktur_utama' => 'Direktur Utama',
            'pcx_manager' => 'PCX Manager',
            'intercomm' => 'Intercomm',
            'manager' => 'Manager',
            default => 'Employee',
        };
    }

    private function resolveDemoRoleDescription(string $roleKey): string
    {
        return match ($roleKey) {
            'direktur_utama' => 'Akun pimpinan utama.',
            'pcx_manager' => 'Akun pengelola PCX dan Intercomm.',
            'intercomm' => 'Akun tim Intercomm.',
            'manager' => 'Akun level manager.',
            default => 'Akun karyawan seeded.',
        };
    }

    private function resolveDemoTone(string $roleKey): string
    {
        return match ($roleKey) {
            'direktur_utama' => 'slate',
            'pcx_manager' => 'emerald',
            'intercomm' => 'blue',
            'manager' => 'amber',
            default => 'rose',
        };
    }

    private function resolveDivisionTone(?string $divisionName): string
    {
        $normalized = mb_strtolower(trim((string) $divisionName));

        return match (true) {
            str_contains($normalized, 'human resource'),
            str_contains($normalized, 'human resources'),
            str_contains($normalized, 'hr') => 'emerald',
            str_contains($normalized, 'information tech'),
            str_contains($normalized, 'it') => 'amber',
            str_contains($normalized, 'os') => 'rose',
            default => 'slate',
        };
    }

    private function resolveDivisionBadgeClass(?string $divisionName): string
    {
        $tone = $this->resolveDivisionTone($divisionName);

        return match ($tone) {
            'emerald' => 'border-emerald-300/40 bg-emerald-400/15 text-emerald-100',
            'amber' => 'border-amber-300/40 bg-amber-400/15 text-amber-100',
            'rose' => 'border-rose-300/40 bg-rose-400/15 text-rose-100',
            default => 'border-slate-300/40 bg-slate-400/15 text-slate-100',
        };
    }

    private function resolveDivisionRank(?string $divisionName): int
    {
        $normalized = mb_strtolower(trim((string) $divisionName));

        if ($normalized === '') {
            return 99;
        }

        if (str_contains($normalized, 'human resource') || str_contains($normalized, 'human resources') || str_contains($normalized, 'hr')) {
            return 1;
        }

        if (str_contains($normalized, 'information tech') || str_contains($normalized, 'it')) {
            return 2;
        }

        if (str_contains($normalized, 'os')) {
            return 3;
        }

        return 9;
    }

    private function buildDefaultDemoPassword(string $employeeName, string $employeeNumber): string
    {
        $firstName = preg_split('/\s+/', trim($employeeName))[0] ?? '';
        $firstName = Str::of($firstName)->lower()->replaceMatches('/[^a-z0-9]/', '')->value();

        if ($firstName === '') {
            $firstName = 'user';
        }

        $nipDigits = preg_replace('/\D+/', '', $employeeNumber) ?? '';
        $suffix = $nipDigits === '' ? '00' : str_pad(substr($nipDigits, -2), 2, '0', STR_PAD_LEFT);

        return $firstName . $suffix;
    }
}
