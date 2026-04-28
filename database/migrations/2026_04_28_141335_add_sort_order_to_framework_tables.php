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
        Schema::table('vnb_framework_behaviours', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0)->after('name');
        });

        Schema::table('vnb_framework_stage_configs', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0)->after('label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vnb_framework_stage_configs', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('vnb_framework_behaviours', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
