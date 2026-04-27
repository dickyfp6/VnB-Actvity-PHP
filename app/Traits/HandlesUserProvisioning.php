<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

trait HandlesUserProvisioning
{
    /**
     * Ensure an employee has a user account.
     * If missing, create one. If exists, sync basic info.
     */
    protected function provisionEmployeeUserAccount(Employee $employee, bool $allowCreate = true): ?string
    {
        $email = trim((string) $employee->email);
        if ($email === '') {
            return null;
        }

        $existingByEmployee = User::query()->where('employee_id', $employee->id)->first();
        $existingByEmail = User::query()->whereRaw('LOWER(email) = ?', [Str::lower($email)])->first();

        // Handle edge cases where email and employee_id associations might conflict
        if ($existingByEmployee && $existingByEmail && $existingByEmployee->id !== $existingByEmail->id) {
            // Ideally should not happen if data is clean
            return null; 
        }

        $user = $existingByEmployee ?: $existingByEmail;

        if (!$user && !$allowCreate) {
            return null;
        }

        if ($user) {
            $user->update([
                'name' => $employee->name,
                'email' => $email,
                'phone' => $employee->whatsapp,
                'status' => strtolower($employee->status ?? 'active') === 'aktif' ? 'active' : 'inactive',
                'employee_id' => $employee->id,
            ]);

            $this->ensureBaseEmployeeRole($user);
            return null;
        }

        // Create new account
        $rawPassword = $this->buildDefaultPasswordFromEmployee($employee);

        $user = User::create([
            'name' => $employee->name,
            'email' => $email,
            'password' => Hash::make($rawPassword),
            'temp_password_encrypted' => Crypt::encryptString($rawPassword),
            'temp_password_generated_at' => now(),
            'phone' => $employee->whatsapp,
            'status' => strtolower($employee->status ?? 'active') === 'aktif' ? 'active' : 'inactive',
            'employee_id' => $employee->id,
            'email_verified_at' => now(),
        ]);

        $this->ensureBaseEmployeeRole($user);

        return $rawPassword;
    }

    protected function ensureBaseEmployeeRole(User $user): void
    {
        $roleName = 'employee';
        // Create role if not exists
        Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        
        if (!$user->hasRole($roleName)) {
            $user->assignRole($roleName);
        }
    }

    protected function buildDefaultPasswordFromEmployee(Employee $employee): string
    {
        $name = trim((string) $employee->name);
        $firstName = preg_split('/\s+/', $name)[0] ?? '';
        $firstName = Str::of($firstName)->lower()->replaceMatches('/[^a-z0-9]/', '')->value();

        if ($firstName === '') {
            $firstName = 'user';
        }

        $nipDigits = preg_replace('/\D+/', '', (string) ($employee->employee_number ?? '')) ?? '';
        $suffix = $nipDigits === '' ? '00' : str_pad(substr($nipDigits, -2), 2, '0', STR_PAD_LEFT);

        return $firstName . $suffix;
    }
}
