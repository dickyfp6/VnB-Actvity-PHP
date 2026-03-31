<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        DB::statement("UPDATE employees
            SET employee_status = CASE
                WHEN LOWER(TRIM(employee_status)) IN ('os', 'outsourcing', 'outsource') THEN 'OS'
                WHEN LOWER(TRIM(employee_status)) IN ('pkwt', 'inactive', 'resigned', 'terminated', 'leave') THEN 'PKWT'
                ELSE 'PKWTT'
            END");

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE employees MODIFY employee_status ENUM('PKWTT','PKWT','OS') NOT NULL DEFAULT 'PKWTT'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE employees MODIFY employee_status ENUM('active','inactive','resigned','terminated','leave','PKWTT','PKWT','OS') NOT NULL DEFAULT 'active'");
        }

        DB::statement("UPDATE employees
            SET employee_status = CASE
                WHEN UPPER(employee_status) = 'OS' THEN 'active'
                WHEN UPPER(employee_status) = 'PKWT' THEN 'inactive'
                ELSE 'active'
            END");
    }
};
