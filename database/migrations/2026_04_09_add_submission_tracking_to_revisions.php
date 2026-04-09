<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing columns to vnb_plan_revisions table to support version control
        // These columns are needed for the submitForApproval flow
        if (Schema::hasTable('vnb_plan_revisions') && !Schema::hasColumn('vnb_plan_revisions', 'version_number')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                // Add version_number column (if not exists)
                if (!Schema::hasColumn('vnb_plan_revisions', 'version_number')) {
                    // Add as the 3rd column (after id, vnb_plan_id)
                    $table->unsignedInteger('version_number')->default(1)->comment('Version number for submissions');
                }
            });
        }
        
        if (Schema::hasTable('vnb_plan_revisions') && !Schema::hasColumn('vnb_plan_revisions', 'submitted_by')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete()->comment('User who submitted the plan');
            });
        }
        
        if (Schema::hasTable('vnb_plan_revisions') && !Schema::hasColumn('vnb_plan_revisions', 'snapshot')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                $table->json('snapshot')->nullable()->comment('JSON snapshot of plan state at submission time');
            });
        }
        
        if (Schema::hasTable('vnb_plan_revisions') && !Schema::hasColumn('vnb_plan_revisions', 'decision')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                $table->enum('decision', ['approve', 'reject'])->nullable()->comment('Manager decision: approve or reject');
            });
        }
        
        if (Schema::hasTable('vnb_plan_revisions') && !Schema::hasColumn('vnb_plan_revisions', 'review_notes')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                $table->text('review_notes')->nullable()->comment('Notes from manager review');
            });
        }
        
        if (Schema::hasTable('vnb_plan_revisions') && !Schema::hasColumn('vnb_plan_revisions', 'reviewed_at')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                $table->dateTime('reviewed_at')->nullable()->comment('Timestamp when manager reviewed');
            });
        }
        
        if (Schema::hasTable('vnb_plan_revisions') && !Schema::hasColumn('vnb_plan_revisions', 'reviewed_by')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->comment('Manager who reviewed');
            });
        }
    }

    public function down(): void
    {
        // Drop the added columns
        if (Schema::hasTable('vnb_plan_revisions')) {
            Schema::table('vnb_plan_revisions', function (Blueprint $table) {
                $columns = ['version_number', 'submitted_by', 'snapshot', 'decision', 'review_notes', 'reviewed_at', 'reviewed_by'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('vnb_plan_revisions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
