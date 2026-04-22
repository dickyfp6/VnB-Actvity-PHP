<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_source_employees', function (Blueprint $table) {
            $table->id();
            $table->enum('source_system', ['HRIS', 'HRMS']);
            $table->string('employee_number', 50);
            $table->string('name', 255);
            $table->date('date_joined');
            $table->string('email', 255)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('division', 150)->nullable();
            $table->string('department', 150)->nullable();
            $table->string('position', 150)->nullable();
            $table->string('placement', 150)->nullable();
            $table->string('level', 80)->nullable();
            $table->enum('employee_status', ['PKWTT', 'PKWT', 'OS'])->default('PKWTT');
            $table->timestamps();

            $table->unique(['source_system', 'employee_number'], 'sync_source_unique_system_employee');
            $table->index('employee_number');
            $table->index('source_system');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_source_employees');
    }
};
