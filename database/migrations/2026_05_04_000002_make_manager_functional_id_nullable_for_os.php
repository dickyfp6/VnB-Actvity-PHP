<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('manager_functional_id')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('manager_functional_id')
                ->nullable(false)
                ->change();
        });
    }
};
