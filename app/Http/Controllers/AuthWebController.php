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
        return view('auth.login');
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
}
