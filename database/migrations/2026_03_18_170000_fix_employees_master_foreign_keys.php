<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        $tempTable = 'employees_fk_fix_backup';

        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable($tempTable)) {
            Schema::drop($tempTable);
        }

        DB::statement("CREATE TABLE {$tempTable} AS SELECT
            id, employee_number, name, date_joined, induction_date, company,
            division_id, department_id, position_id, placement, level, employee_status,
            email, whatsapp, manager_functional_id, manager_operational_id,
            vnb_period_start, vnb_period_end, vnb_status, notes,
            created_at, updated_at, deleted_at
            FROM employees");

        Schema::drop('employees');

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number', 50)->unique();
            $table->string('name', 255);
            $table->date('date_joined');
            $table->date('induction_date')->nullable();
            $table->string('company', 100);
            $table->foreignId('division_id')->nullable()->constrained('master_divisions');
            $table->foreignId('department_id')->nullable()->constrained('master_departments');
            $table->foreignId('position_id')->nullable()->constrained('master_positions');
            $table->string('placement', 100)->nullable();
            $table->string('level', 50)->nullable();
            $table->string('employee_status', 50)->default('PKWTT');
            $table->string('email', 255)->unique();
            $table->string('whatsapp', 20)->nullable();
            $table->foreignId('manager_functional_id')->nullable()->constrained('managers', 'id');
            $table->foreignId('manager_operational_id')->nullable()->constrained('managers', 'id');
            $table->date('vnb_period_start')->nullable();
            $table->date('vnb_period_end')->nullable();
            $table->enum('vnb_status', ['not_started', 'active', 'completed', 'canceled'])->default('not_started');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("INSERT INTO employees (
            id, employee_number, name, date_joined, induction_date, company,
            division_id, department_id, position_id, placement, level, employee_status,
            email, whatsapp, manager_functional_id, manager_operational_id,
            vnb_period_start, vnb_period_end, vnb_status, notes,
            created_at, updated_at, deleted_at
        )
        SELECT
            id, employee_number, name, date_joined, induction_date, company,
            division_id, department_id, position_id, placement, level,
            CASE
                WHEN LOWER(TRIM(employee_status)) IN ('os', 'outsourcing', 'outsource') THEN 'OS'
                WHEN LOWER(TRIM(employee_status)) IN ('pkwt', 'inactive', 'resigned', 'terminated', 'leave') THEN 'PKWT'
                ELSE 'PKWTT'
            END AS employee_status,
            email, whatsapp, manager_functional_id, manager_operational_id,
            vnb_period_start, vnb_period_end, vnb_status, notes,
            created_at, updated_at, deleted_at
        FROM {$tempTable}");

        Schema::drop($tempTable);

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
    }
};
