<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->foreignId('manager_functional_id')->nullable()->constrained('employees', 'id');
            $table->foreignId('manager_operational_id')->nullable()->constrained('employees', 'id');
            $table->date('vnb_period_start')->nullable();
            $table->date('vnb_period_end')->nullable();
            $table->enum('vnb_status', ['not_started', 'active', 'completed', 'canceled'])->default('not_started');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_status', 'vnb_status']);
            $table->index(['date_joined', 'induction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
