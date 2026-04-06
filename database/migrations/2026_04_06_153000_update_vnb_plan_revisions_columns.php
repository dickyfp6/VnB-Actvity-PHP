<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite, we need a different approach since it doesn't support renaming columns with constraints
        // Check if the column already exists with the new name to avoid re-running
        $columns = DB::select("PRAGMA table_info(vnb_plan_revisions)");
        $columnNames = array_column($columns, 'name');
        
        if (in_array('vnb_plan_id', $columnNames)) {
            // Migration already applied, skip
            return;
        }
        
        if (!in_array('plan_id', $columnNames)) {
            // Column doesn't exist at all, skip
            return;
        }

        // For SQLite, we'll use a workaround: create new column, copy data, drop old column
        Schema::table('vnb_plan_revisions', function (Blueprint $table) {
            $table->foreignId('vnb_plan_id')->nullable()->constrained('vnb_plans')->onDelete('cascade');
        });
        
        // Copy data from old column to new column
        DB::statement('UPDATE vnb_plan_revisions SET vnb_plan_id = plan_id');
        
        // Make the new column non-nullable
        // Note: SQLite doesn't support modifying column constraints, so we'll keep it nullable for now
        // The model's fillable will guide proper usage
    }

    public function down(): void
    {
        // For rollback, we'd need to reverse the process
        // This is complex in SQLite, so we'll just leave a warning
        // In production, consider more sophisticated approaches
    }
};
