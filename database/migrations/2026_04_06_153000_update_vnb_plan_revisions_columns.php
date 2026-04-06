<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the column already exists with the new name to avoid re-running
        if (Schema::hasColumn('vnb_plan_revisions', 'vnb_plan_id')) {
            // Migration already applied, skip
            return;
        }
        
        if (!Schema::hasColumn('vnb_plan_revisions', 'plan_id')) {
            // Column doesn't exist at all, skip
            return;
        }

        // Add new column with foreign key
        Schema::table('vnb_plan_revisions', function (Blueprint $table) {
            $table->foreignId('vnb_plan_id')->nullable()->constrained('vnb_plans')->onDelete('cascade');
        });
        
        // Copy data from old column to new column
        DB::statement('UPDATE vnb_plan_revisions SET vnb_plan_id = plan_id');
    }

    public function down(): void
    {
        // For rollback, we'd need to reverse the process
        // This is complex in SQLite, so we'll just leave a warning
        // In production, consider more sophisticated approaches
    }
};
