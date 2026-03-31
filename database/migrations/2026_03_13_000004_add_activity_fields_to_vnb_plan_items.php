<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add activity submission fields to vnb_plan_items (UC006 & UC007)
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->text('activity_description')->nullable()->after('deliverables')
                  ->comment('UC006: execution description filled by New Hire');
            $table->date('activity_date')->nullable()->after('activity_description')
                  ->comment('UC006: actual date the activity was done');
            $table->enum('submission_status', [
                'draft', 'waiting_approval', 'revision_required', 'completed', 'overdue'
            ])->default('draft')->after('activity_date');
            $table->text('revision_notes')->nullable()->after('submission_status');
            $table->dateTime('submitted_at')->nullable()->after('revision_notes');
            $table->unsignedBigInteger('approved_functional_by')->nullable()->after('submitted_at');
            $table->dateTime('approved_functional_at')->nullable()->after('approved_functional_by');
            $table->unsignedBigInteger('approved_operational_by')->nullable()->after('approved_functional_at');
            $table->dateTime('approved_operational_at')->nullable()->after('approved_operational_by');
            $table->date('due_date')->nullable()->after('approved_operational_at')
                  ->comment('25th of last month of phase, auto-calculated');
        });
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->dropColumn([
                'activity_description', 'activity_date', 'submission_status',
                'revision_notes', 'submitted_at', 'approved_functional_by',
                'approved_functional_at', 'approved_operational_by',
                'approved_operational_at', 'due_date',
            ]);
        });
    }
};
