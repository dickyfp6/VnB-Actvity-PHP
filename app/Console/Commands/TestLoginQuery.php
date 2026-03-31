<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestLoginQuery extends Command
{
    protected $signature = 'debug:test-login {credential}';
    protected $description = 'Test login query with email or NIP';

    public function handle()
    {
        $credential = trim($this->argument('credential'));
        $this->info("Testing login with credential: '{$credential}'\n");

        // Test query
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($credential)])
            ->orWhereHas('employee', function ($query) use ($credential) {
                $query->where('employee_number', $credential);
            })
            ->first();

        if ($user) {
            $this->info("✓ User found!");
            $this->line("  ID: {$user->id}");
            $this->line("  Name: {$user->name}");
            $this->line("  Email: {$user->email}");
            $this->line("  Employee ID: {$user->employee_id}");
            if ($user->employee) {
                $this->line("  Employee Number: {$user->employee->employee_number}");
            }
        } else {
            $this->error("✗ User NOT found!");
            
            // Debug: show all employees with their numbers
            $this->info("\nAvailable employees:");
            $employees = \App\Models\Employee::select('id', 'name', 'employee_number')->get();
            foreach ($employees as $emp) {
                $this->line("  - {$emp->employee_number}: {$emp->name}");
            }
            
            // Debug: show all users
            $this->info("\nAvailable users:");
            $users = User::select('id', 'name', 'email', 'employee_id')->get();
            foreach ($users as $u) {
                $this->line("  - {$u->email} (emp_id: {$u->employee_id})");
            }
        }

        $this->info("\n");
    }
}
