<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vnb_activity_assignments', function (Blueprint $table) {
            $table->date('induction_date')->nullable()->after('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::table('vnb_activity_assignments', function (Blueprint $table) {
            $table->dropColumn('induction_date');
        });
    }
};