<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixUserEmployeeLink extends Command
{
    protected $signature = 'debug:fix-user-link {user_id} {employee_id?}';
    protected $description = 'Fix user-employee relationship';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $employeeId = $this->argument('employee_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User {$userId} not found");
            return 1;
        }

        $this->info("Before: User {$userId} ({$user->name}) -> Employee ID: " . ($user->employee_id ?? 'NULL'));

        if ($employeeId === null) {
            $user->update(['employee_id' => null]);
            $this->info("After: Unlinked from employee");
        } else {
            $user->update(['employee_id' => $employeeId]);
            $this->info("After: Linked to Employee ID {$employeeId}");
        }

        $user->refresh();
        $this->info("Updated: User {$userId} ({$user->name}) -> Employee ID: " . ($user->employee_id ?? 'NULL'));
    }
}
