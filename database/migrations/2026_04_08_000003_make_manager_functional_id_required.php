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
