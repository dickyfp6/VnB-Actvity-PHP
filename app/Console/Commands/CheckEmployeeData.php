<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Console\Command;

class CheckEmployeeData extends Command
{
    protected $signature = 'debug:check-employee';
    protected $description = 'Check employee data for Deepseol user';

    public function handle()
    {
        $this->info('=== DEEPSEOL USER & EMPLOYEE DATA ===\n');

        // Get Deepseol user
        $user = User::where('name', 'Deepseol')->first();
        if (!$user) {
            $this->error('User Deepseol not found!');
            return 1;
        }

        $this->line("User ID: {$user->id}");
        $this->line("Name: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line("Employee ID: {$user->employee_id}");

        if ($user->employee_id) {
            $employee = Employee::find($user->employee_id);
            if ($employee) {
                $this->info("\n✓ Employee found:");
                $this->line("  ID: {$employee->id}");
                $this->line("  Name: {$employee->name}");
                $this->line("  Employee Number (NIP): {$employee->employee_number}");
                $this->line("  Level: {$employee->level}");
                $this->line("  Induction Date: {$employee->induction_date}");
            } else {
                $this->warn("✗ Employee ID {$user->employee_id} not found in database!");
            }
        } else {
            $this->warn("✗ User not linked to any employee!");
        }

        $this->info("\n");
    }
}
