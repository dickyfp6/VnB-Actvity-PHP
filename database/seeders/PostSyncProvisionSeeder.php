<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\User;
use App\Traits\HandlesUserProvisioning;
use Illuminate\Support\Facades\Log;

class PostSyncProvisionSeeder extends Seeder
{
    use HandlesUserProvisioning;

    public function run(): void
    {
        $this->command->info('➡️ Running post-sync provisioning: creating users and assigning manager roles');

        $employees = Employee::all();
        $processed = 0;

        foreach ($employees as $employee) {
            // Ensure user account exists and base employee role assigned
            $this->provisionEmployeeUserAccount($employee, true);
            $processed++;
        }

        // Ensure managers table links to user and assign manager role
        $managers = Manager::all();
        foreach ($managers as $manager) {
            $email = trim((string) $manager->email);
            if ($email === '') {
                $this->command->info("- Manager {$manager->name} has no email, skipping link");
                continue;
            }

            $user = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
            if (!$user) {
                $this->command->info("- No user for manager {$manager->name} <{$email}>, skipping");
                continue;
            }

            if (!$manager->user_id || $manager->user_id !== $user->id) {
                $manager->user_id = $user->id;
                $manager->save();
                $this->command->info("✓ Linked manager {$manager->name} to user {$user->email}");
            }

            if (!$user->hasRole('manager')) {
                $user->assignRole('manager');
                $this->command->info("  -> assigned 'manager' role to {$user->email}");
            }

            if (!$user->last_active_role) {
                $user->update(['last_active_role' => 'manager']);
            }
        }

        $this->command->info("✅ Provisioning finished. Employees processed: {$processed}");
    }
}
