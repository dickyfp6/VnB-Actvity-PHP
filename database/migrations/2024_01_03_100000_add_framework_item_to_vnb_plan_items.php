<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            // Add framework_item_id foreign key
            $table->foreignId('framework_item_id')->nullable()->after('plan_id')->constrained('vnb_framework_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\VnbFrameworkItem::class, 'framework_item_id');
        });
    }
};
