<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class IntercommController extends Controller
{
    /**
     * UC001: List intercomm users
     * Hanya PCX Manager yang dapat mengakses
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasRole('pcx_manager'), 403, 'Hanya PCX Manager yang dapat mengelola Intercomm');
        $query = User::role('intercomm');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('name')->get(['id', 'name', 'email', 'status', 'created_at']),
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
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $rawPassword = (string) random_int(100000, 999999);

        $user = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'password'           => Hash::make($rawPassword),
            'status'             => 'active',
            'email_verified_at'  => now(),
        ]);

        $user->assignRole('intercomm');

        // In production: send email with password. For now return it in response.
        return response()->json([
            'success'       => true,
            'message'       => 'Akun Intercomm berhasil dibuat. Password telah dikirim ke email.',
            'data'          => $user->only('id', 'name', 'email', 'status'),
            '_dev_password' => $rawPassword, // remove in production
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
