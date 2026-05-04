<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\User;
use App\Traits\HandlesUserProvisioning;
use Illuminate\Support\Facades\Log;

class EmployeeObserver
{
    use HandlesUserProvisioning;

    /**
     * Handle the Employee "creating" event.
     * Auto-assign functional manager if not already set.
     */
    public function creating(Employee $employee): void
    {
        // Only auto-assign if manager_functional_id is not already set
        if ($employee->manager_functional_id === null) {
            $manager = $employee->findFunctionalManager();
            if ($manager) {
                $employee->manager_functional_id = $manager->id;
            }
        }
    }

    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        $this->provisionEmployeeUserAccount($employee);
    }

    /**
     * Handle the Employee "updated" event.
     * 
     * When an employee is assigned as a functional or operational manager,
     * automatically create/update their Manager record and assign them the manager role.
     */
    public function updated(Employee $employee): void
    {
        // Keep user info in sync
        if ($employee->wasChanged(['name', 'email', 'whatsapp', 'status'])) {
            $this->provisionEmployeeUserAccount($employee);
        }

        // Check if manager_functional_id or manager_operational_id changed
        if (!$employee->wasChanged(['manager_functional_id', 'manager_operational_id'])) {
            return;
        }

        // Process functional manager
        if ($employee->manager_functional_id) {
            $this->syncManagerRole($employee->manager_functional_id, 'functional');
        }

        // Process operational manager
        if ($employee->manager_operational_id) {
            $this->syncManagerRole($employee->manager_operational_id, 'operational');
        }
    }

    /**
     * Sync manager role when a manager is assigned to an employee
     * 
     * @param int $managerId The manager ID to sync
     * @param string $type 'functional' or 'operational'
     */
    private function syncManagerRole(int $managerId, string $type): void
    {
        try {
            // Get the manager record
            $manager = Manager::find($managerId);
            if (!$manager) {
                Log::warning("Manager ID {$managerId} not found when syncing employee role", [
                    'type' => $type,
                ]);
                return;
            }

            // If manager already has user_id, they're already synced
            if ($manager->user_id) {
                return;
            }

            // Find user by manager email
            $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $manager->email))])
                ->first();

            if (!$user) {
                Log::warning("User not found for manager {$manager->name} ({$manager->email})", [
                    'manager_id' => $managerId,
                    'type' => $type,
                ]);
                return;
            }

            // Link manager to user
            $manager->update(['user_id' => $user->id]);

            // Assign manager role to user if not already assigned
            if (!$user->hasRole('manager')) {
                $user->assignRole('manager');
                Log::info("Manager role assigned to user {$user->email}", [
                    'manager_id' => $managerId,
                    'user_id' => $user->id,
                    'type' => $type,
                ]);
            }

            // If user doesn't have any active role set, set manager as last_active_role
            if (!$user->last_active_role) {
                $user->update(['last_active_role' => 'manager']);
            }
        } catch (\Exception $e) {
            Log::error("Error syncing manager role for manager ID {$managerId}", [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
