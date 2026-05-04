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
        Schema::table('sync_source_employees', function (Blueprint $table) {
            $table->string('manager_functional')->nullable()->after('level');
            $table->string('manager_operational')->nullable()->after('manager_functional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_source_employees', function (Blueprint $table) {
            $table->dropColumn(['manager_functional', 'manager_operational']);
        });
    }
};
