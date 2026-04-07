<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->text('integration_1')->nullable()->after('description')->comment('Integrasi pengukuran 1');
            $table->text('integration_2')->nullable()->after('integration_1')->comment('Integrasi pengukuran 2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->dropColumn(['integration_1', 'integration_2']);
        });
    }
};
