<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('employee_number', 50);
            $table->string('name', 255);
            $table->date('date_joined');
            $table->date('induction_date')->nullable();
            $table->string('company', 100)->nullable();
            $table->foreignId('division_id')->nullable()->constrained('master_divisions');
            $table->foreignId('department_id')->nullable()->constrained('master_departments');
            $table->foreignId('position_id')->nullable()->constrained('master_positions');
            $table->string('placement', 100)->nullable();
            $table->string('level', 50)->nullable();
            $table->string('employee_status', 50);
            $table->string('email', 255);
            $table->string('whatsapp', 20)->nullable();
            $table->foreignId('manager_functional_id')->nullable()->constrained('employees', 'id');
            $table->foreignId('manager_operational_id')->nullable()->constrained('employees', 'id');
            $table->string('career_stage')->nullable();
            $table->string('employment_state')->nullable();
            $table->dateTime('status_changed_at')->nullable();
            $table->string('status_change_reason')->nullable();
            $table->integer('status_changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Aktif', 'Inactive'])->default('Inactive');
            $table->dateTime('moved_to_history_at');
            $table->timestamps();

            $table->index('employee_id');
            $table->index('employee_number');
            $table->index('status');
            $table->index('moved_to_history_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_histories');
    }
};
