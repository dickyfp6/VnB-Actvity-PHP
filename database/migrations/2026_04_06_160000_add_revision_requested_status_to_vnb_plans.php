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
            DB::statement('DROP TABLE IF EXISTS vnb_plan_items_old');
            DB::statement('DROP INDEX IF EXISTS vnb_plans_employee_id_status_index');
            DB::statement('DROP INDEX IF EXISTS vnb_plans_period_id_status_index');
            
            // Only backup if table exists
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('vnb_plans', 'vnb_plan_items')");
            $tableNames = array_column($tables, 'name');
            
            if (in_array('vnb_plans', $tableNames)) {
                DB::statement('ALTER TABLE vnb_plans RENAME TO vnb_plans_old');
            }
            if (in_array('vnb_plan_items', $tableNames)) {
                DB::statement('ALTER TABLE vnb_plan_items RENAME TO vnb_plan_items_old');
            }
            
            // Recreate vnb_plans with updated enum
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
            
            // Recreate vnb_plan_items with correct FK reference and full schema
            Schema::create('vnb_plan_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('plan_id')->constrained('vnb_plans')->onDelete('cascade');
                $table->string('activity_title', 255);
                $table->text('description');
                $table->date('implementation_date');
                $table->text('deliverables');
                $table->json('behavior_metrics')->nullable();
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'not_achieved'])->default('not_started');
                $table->integer('completion_percentage')->default(0);
                // Activity submission fields
                $table->text('activity_description')->nullable();
                $table->date('activity_date')->nullable();
                $table->enum('submission_status', ['draft', 'waiting_approval', 'revision_required', 'completed', 'overdue'])->default('draft');
                $table->text('revision_notes')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->unsignedBigInteger('approved_functional_by')->nullable();
                $table->dateTime('approved_functional_at')->nullable();
                $table->unsignedBigInteger('approved_operational_by')->nullable();
                $table->dateTime('approved_operational_at')->nullable();
                $table->date('due_date')->nullable();
                $table->timestamps();
            });
            
            // Drop existing index if it exists and recreate it
            DB::statement('DROP INDEX IF EXISTS vnb_plan_items_plan_id_status_index');
            DB::statement('CREATE INDEX vnb_plan_items_plan_id_status_index ON vnb_plan_items (plan_id, status)');
            
            // Copy data back if it exists
            $hasOldData = DB::select("SELECT COUNT(*) as cnt FROM sqlite_master WHERE type='table' AND name='vnb_plans_old'");
            if (!empty($hasOldData) && $hasOldData[0]->cnt > 0) {
                DB::statement('INSERT INTO vnb_plans (id, employee_id, period_id, phase_number, title, description, planning_mode, status, revision_count, revision_notes, submitted_at, approved_at, approved_by, rejection_reason, discussion_notes, created_at, updated_at) 
                              SELECT id, employee_id, period_id, phase_number, title, description, planning_mode, status, revision_count, revision_notes, submitted_at, approved_at, approved_by, rejection_reason, discussion_notes, created_at, updated_at FROM vnb_plans_old');
                
                DB::statement('INSERT INTO vnb_plan_items (id, plan_id, activity_title, description, implementation_date, deliverables, behavior_metrics, status, completion_percentage, activity_description, activity_date, submission_status, revision_notes, submitted_at, approved_functional_by, approved_functional_at, approved_operational_by, approved_operational_at, due_date, created_at, updated_at) 
                              SELECT id, plan_id, activity_title, description, implementation_date, deliverables, behavior_metrics, status, completion_percentage, activity_description, activity_date, submission_status, revision_notes, submitted_at, approved_functional_by, approved_functional_at, approved_operational_by, approved_operational_at, due_date, created_at, updated_at FROM vnb_plan_items_old');
            }
            
            // Cleanup old tables
            DB::statement('DROP TABLE IF EXISTS vnb_plans_old');
            DB::statement('DROP TABLE IF EXISTS vnb_plan_items_old');
        } else {
            // For MySQL/PostgreSQL
            DB::statement("ALTER TABLE vnb_plans MODIFY status ENUM('draft', 'waiting_manager_approval', 'approved', 'rejected', 'in_progress', 'submitted', 'revision_requested') DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        // For down, just remove the status value from enum  
        // This is handled by Laravel's migration system
    }
};
