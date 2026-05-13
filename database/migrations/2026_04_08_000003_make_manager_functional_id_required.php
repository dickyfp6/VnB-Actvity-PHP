<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make manager_functional_id NOT NULLABLE (required field)
     * Rule: Every employee MUST have a functional manager
     * Operational manager is OPTIONAL (can be null)
     * 
     * NOTE: This migration is superseded by 2026_05_04_000002 which reverts to NULLABLE
     * to support OS (Outsource) employees. Keeping this for reference but it will be rolled back.
     */
    public function up(): void
    {
        // First, update any existing null values to a default manager.
        // Avoid MySQL 1093 by selecting the ID separately before update.
        $defaultManagerId = DB::table('employees')
            ->where('id', '<', 1000)
            ->value('id');

        if ($defaultManagerId === null) {
            $defaultManagerId = DB::table('employees')->value('id');
        }

        if ($defaultManagerId !== null) {
            DB::table('employees')
                ->whereNull('manager_functional_id')
                ->update(['manager_functional_id' => $defaultManagerId]);
        }

        // Use raw SQL - more robust than Schema::change() for this type of modification
        DB::statement('ALTER TABLE employees MODIFY manager_functional_id BIGINT UNSIGNED NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Use raw SQL to change column back to nullable
        DB::statement('ALTER TABLE employees MODIFY manager_functional_id BIGINT UNSIGNED NULL');
    }
};

