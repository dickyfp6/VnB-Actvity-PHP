<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->json('activity_rows')->nullable()->after('activity_date');
        });
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->dropColumn('activity_rows');
        });
    }
};