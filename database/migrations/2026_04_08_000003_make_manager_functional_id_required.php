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
     */
    public function up(): void
    {
        // First, update any existing null values to a default manager
        // This is a safety measure before making the column NOT NULL
        DB::statement('
            UPDATE employees 
            SET manager_functional_id = (SELECT id FROM employees WHERE id < 1000 LIMIT 1)
            WHERE manager_functional_id IS NULL
        ');

        Schema::table('employees', function (Blueprint $table) {
            // Make manager_functional_id NOT NULL
            $table->foreignId('manager_functional_id')
                ->nullable(false)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert to nullable
            $table->foreignId('manager_functional_id')
                ->nullable()
                ->change();
        });
    }
};
