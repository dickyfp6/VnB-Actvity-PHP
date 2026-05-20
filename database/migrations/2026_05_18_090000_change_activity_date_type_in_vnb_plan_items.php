<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->text('activity_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            $table->date('activity_date')->nullable()->change();
        });
    }
};
