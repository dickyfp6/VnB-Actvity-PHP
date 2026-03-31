<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('master_employee_statuses')) {
            Schema::create('master_employee_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $now = now();
        foreach (['PKWTT', 'PKWT', 'OS'] as $status) {
            $exists = DB::table('master_employee_statuses')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($status)])
                ->exists();

            if (!$exists) {
                DB::table('master_employee_statuses')->insert([
                    'name' => $status,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]);
            }
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'employee_status')) {
            DB::statement("UPDATE employees
                SET employee_status = CASE
                    WHEN LOWER(TRIM(employee_status)) IN ('os', 'outsourcing', 'outsource') THEN 'OS'
                    WHEN LOWER(TRIM(employee_status)) IN ('pkwt', 'inactive', 'resigned', 'terminated', 'leave') THEN 'PKWT'
                    ELSE 'PKWTT'
                END");

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE employees MODIFY employee_status VARCHAR(50) NOT NULL DEFAULT 'PKWTT'");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'employee_status') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE employees MODIFY employee_status ENUM('PKWTT','PKWT','OS') NOT NULL DEFAULT 'PKWTT'");
        }

        Schema::dropIfExists('master_employee_statuses');
    }
};
