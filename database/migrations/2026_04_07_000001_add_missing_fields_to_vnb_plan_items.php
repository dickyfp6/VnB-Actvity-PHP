<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing fields to vnb_plan_items if they don't exist
        if (Schema::hasTable('vnb_plan_items')) {
            Schema::table('vnb_plan_items', function (Blueprint $table) {
                if (!Schema::hasColumn('vnb_plan_items', 'implementation_date')) {
                    $table->date('implementation_date')->nullable()->after('integration_2');
                }
                if (!Schema::hasColumn('vnb_plan_items', 'framework_item_id')) {
                    $table->unsignedBigInteger('framework_item_id')->nullable()->after('plan_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vnb_plan_items')) {
            Schema::table('vnb_plan_items', function (Blueprint $table) {
                $table->dropColumn(['implementation_date', 'framework_item_id']);
            });
        }
    }
};
