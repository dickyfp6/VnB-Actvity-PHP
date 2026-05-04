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
        Schema::table('managers', function (Blueprint $table) {
            // Add division and department foreign keys for manager hierarchy
            $table->unsignedBigInteger('division_id')->nullable()->after('division');
            $table->unsignedBigInteger('department_id')->nullable()->after('division_id');

            // Foreign key constraints
            $table->foreign('division_id')
                ->references('id')
                ->on('master_divisions')
                ->onDelete('set null');

            $table->foreign('department_id')
                ->references('id')
                ->on('master_departments')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['division_id', 'department_id']);
        });
    }
};
