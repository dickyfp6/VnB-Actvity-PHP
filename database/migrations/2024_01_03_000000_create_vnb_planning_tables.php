<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vnb_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('phase_number')->comment('1, 2, 3');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('cutoff_date')->comment('25th of month');
            $table->enum('status', ['not_started', 'in_progress', 'ready_for_presentation', 'submitted', 'completed', 'rejected'])->default('not_started');
            $table->timestamps();

            $table->unique(['employee_id', 'phase_number']);
            $table->index(['employee_id', 'status']);
        });

        Schema::create('vnb_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('period_id')->constrained('vnb_periods')->onDelete('cascade');
            $table->tinyInteger('phase_number');
            $table->string('title', 255);
            $table->text('description');
            $table->enum('planning_mode', ['adjust_all', 'custom'])->default('custom');
            $table->enum('status', ['draft', 'waiting_manager_approval', 'approved', 'rejected', 'in_progress', 'submitted'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('employees', 'id');
            $table->text('rejection_reason')->nullable();
            $table->text('discussion_notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['period_id', 'status']);
        });

        Schema::create('vnb_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('vnb_plans')->onDelete('cascade');
            $table->string('activity_title', 255);
            $table->text('description');
            $table->date('implementation_date');
            $table->text('deliverables');
            $table->json('behavior_metrics')->nullable()->comment('checklist items');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'not_achieved'])->default('not_started');
            $table->integer('completion_percentage')->default(0);
            $table->timestamps();

            $table->index(['plan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnb_plan_items');
        Schema::dropIfExists('vnb_plans');
        Schema::dropIfExists('vnb_periods');
    }
};
