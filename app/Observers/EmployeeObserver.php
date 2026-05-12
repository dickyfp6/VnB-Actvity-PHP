<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\User;
use App\Traits\HandlesUserProvisioning;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EmployeeObserver
{
    use HandlesUserProvisioning;

    /**
     * Handle the Employee "saving" event (before create/update).
     * Auto-sync manager names based on IDs.
     */
    public function saving(Employee $employee): void
    {
        // Auto-assign functional manager if creating and not set
        // Skip auto-assignment if manager IDs are already provided (HRIS sync case)
        if (!$employee->exists && $employee->manager_functional_id === null && Schema::hasTable('master_positions')) {
            try {
                $manager = $employee->findFunctionalManager();
                if ($manager) {
                    $employee->manager_functional_id = $manager->id;
                }
            } catch (\Exception $e) {
                // Silent fail during master table missing scenarios
            }
        }

        // Keep manager names in sync with IDs for "easy calling"
        if ($employee->isDirty('manager_functional_id')) {
            $manager = Manager::find($employee->manager_functional_id);
            $employee->manager_functional = $manager ? $manager->name : null;
        }

        if ($employee->isDirty('manager_operational_id')) {
            $manager = Manager::find($employee->manager_operational_id);
            $employee->manager_operational = $manager ? $manager->name : null;
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
        // Keep user info in sync (only if not during batch sync where managers are already set)
        if (!$employee->wasChanged(['manager_functional_id', 'manager_operational_id'])) {
            if ($employee->wasChanged(['name', 'email', 'whatsapp', 'status'])) {
                $this->provisionEmployeeUserAccount($employee);
            }
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
