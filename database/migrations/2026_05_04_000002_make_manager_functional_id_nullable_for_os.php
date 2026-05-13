<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Revert manager_functional_id to NULLABLE to support OS (Outsource) employees.
     * With new auto-assignment logic:
     * - OS employees: manager_functional_id = null (no manager)
     * - Non-OS employees: manager_functional_id auto-assigned by observer during creation
     */
    public function up(): void
    {
        // Use raw SQL to change column to nullable - more robust than Schema::change()
        DB::statement('ALTER TABLE employees MODIFY manager_functional_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ensure ALL NULL values are handled before converting back to NOT NULL
        // manager_functional_id has a foreign key to managers table, so we need to use a valid manager ID
        
        $nullCount = DB::table('employees')->whereNull('manager_functional_id')->count();
        
        if ($nullCount > 0) {
            // Find any valid manager ID from the managers table
            $validManagerId = DB::table('managers')
                ->where('status', 'active')
                ->orderBy('id')
                ->limit(1)
                ->value('id');

            // If no active manager found, try any manager
            if (!$validManagerId) {
                $validManagerId = DB::table('managers')
                    ->orderBy('id')
                    ->limit(1)
                    ->value('id');
            }

            // If still no manager found, we have a problem - can't set a NOT NULL foreign key without valid ref
            if ($validManagerId) {
                DB::table('employees')
                    ->whereNull('manager_functional_id')
                    ->update(['manager_functional_id' => $validManagerId]);
            } else {
                // As a last resort, disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::table('employees')
                    ->whereNull('manager_functional_id')
                    ->update(['manager_functional_id' => 1]);
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }

        // Now safely convert to NOT NULL since all NULLs have been replaced
        DB::statement('ALTER TABLE employees MODIFY manager_functional_id BIGINT UNSIGNED NOT NULL');
    }
};
