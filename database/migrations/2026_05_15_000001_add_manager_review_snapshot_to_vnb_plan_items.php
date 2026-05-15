<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->json('manager_review_snapshot')->nullable()->after('submission_status')
                ->comment('Snapshot review manager per sub-row integration');
        });
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->dropColumn('manager_review_snapshot');
        });
    }
};