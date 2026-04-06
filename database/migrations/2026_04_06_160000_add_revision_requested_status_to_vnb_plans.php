<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            // For SQLite, recreate the table with updated enum
            // Drop indexes & temp tables first if they exist
            DB::statement('DROP TABLE IF EXISTS vnb_plans_old');
            DB::statement('DROP INDEX IF EXISTS vnb_plans_employee_id_status_index');
            DB::statement('DROP INDEX IF EXISTS vnb_plans_period_id_status_index');
            
            DB::statement('ALTER TABLE vnb_plans RENAME TO vnb_plans_old');
            
            Schema::create('vnb_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->foreignId('period_id')->constrained('vnb_periods')->onDelete('cascade');
                $table->tinyInteger('phase_number');
                $table->string('title', 255);
                $table->text('description');
                $table->enum('planning_mode', ['adjust_all', 'custom'])->default('custom');
                $table->enum('status', ['draft', 'waiting_manager_approval', 'approved', 'rejected', 'in_progress', 'submitted', 'revision_requested'])->default('draft');
                $table->integer('revision_count')->default(0)->comment('Total revisions requested');
                $table->text('revision_notes')->nullable()->comment('Current revision notes from manager');
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('employees', 'id');
                $table->text('rejection_reason')->nullable();
                $table->text('discussion_notes')->nullable();
                $table->timestamps();

                $table->index(['employee_id', 'status']);
                $table->index(['period_id', 'status']);
            });
            
            // Copy data, mapping old columns to new columns
            DB::statement('INSERT INTO vnb_plans (id, employee_id, period_id, phase_number, title, description, planning_mode, status, revision_count, revision_notes, submitted_at, approved_at, approved_by, rejection_reason, discussion_notes, created_at, updated_at) 
                          SELECT id, employee_id, period_id, phase_number, title, description, planning_mode, status, revision_count, revision_notes, submitted_at, approved_at, approved_by, rejection_reason, discussion_notes, created_at, updated_at FROM vnb_plans_old');
            
            DB::statement('DROP TABLE vnb_plans_old');
        } else {
            // For MySQL/PostgreSQL
            DB::statement("ALTER TABLE vnb_plans MODIFY status ENUM('draft', 'waiting_manager_approval', 'approved', 'rejected', 'in_progress', 'submitted', 'revision_requested') DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            DB::statement('ALTER TABLE vnb_plans RENAME TO vnb_plans_old');
            
            Schema::create('vnb_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->foreignId('period_id')->constrained('vnb_periods')->onDelete('cascade');
                $table->tinyInteger('phase_number');
                $table->string('title', 255);
                $table->text('description');
                $table->enum('planning_mode', ['adjust_all', 'custom'])->default('custom');
                $table->enum('status', ['draft', 'waiting_manager_approval', 'approved', 'rejected', 'in_progress', 'submitted'])->default('draft');
                $table->integer('revision_count')->default(0)->comment('Total revisions requested');
                $table->text('revision_notes')->nullable()->comment('Current revision notes from manager');
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('employees', 'id');
                $table->text('rejection_reason')->nullable();
                $table->text('discussion_notes')->nullable();
                $table->timestamps();

                $table->index(['employee_id', 'status']);
                $table->index(['period_id', 'status']);
            });
            
            DB::statement('INSERT INTO vnb_plans (id, employee_id, period_id, phase_number, title, description, planning_mode, status, revision_count, revision_notes, submitted_at, approved_at, approved_by, rejection_reason, discussion_notes, created_at, updated_at) 
                          SELECT id, employee_id, period_id, phase_number, title, description, planning_mode, status, revision_count, revision_notes, submitted_at, approved_at, approved_by, rejection_reason, discussion_notes, created_at, updated_at FROM vnb_plans_old');
            
            DB::statement('DROP TABLE vnb_plans_old');
        } else {
            DB::statement("ALTER TABLE vnb_plans MODIFY status ENUM('draft', 'waiting_manager_approval', 'approved', 'rejected', 'in_progress', 'submitted') DEFAULT 'draft'");
        }
    }
};
