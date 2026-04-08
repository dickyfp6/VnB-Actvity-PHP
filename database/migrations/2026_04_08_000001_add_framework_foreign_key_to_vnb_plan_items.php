<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add framework_item_id foreign key to vnb_plan_items
     * This runs AFTER vnb_framework_items is created (migration 2026_03_13_000003)
     */
    public function up(): void
    {
        if (Schema::hasTable('vnb_plan_items') && Schema::hasTable('vnb_framework_items')) {
            Schema::table('vnb_plan_items', function (Blueprint $table) {
                if (!Schema::hasColumn('vnb_plan_items', 'framework_item_id')) {
                    $table->foreignId('framework_item_id')
                        ->nullable()
                        ->constrained('vnb_framework_items')
                        ->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            if (Schema::hasColumn('vnb_plan_items', 'framework_item_id')) {
                $table->dropColumn('framework_item_id');
            }
        });
    }
};
