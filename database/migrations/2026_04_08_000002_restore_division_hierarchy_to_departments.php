<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restore division_id to master_departments for organizational hierarchy
     */
    public function up(): void
    {
        if (!Schema::hasColumn('master_departments', 'division_id')) {
            Schema::table('master_departments', function (Blueprint $table) {
                $table->foreignId('division_id')
                    ->nullable()
                    ->constrained('master_divisions')
                    ->onDelete('cascade')
                    ->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('master_departments', function (Blueprint $table) {
            if (Schema::hasColumn('master_departments', 'division_id')) {
                $table->dropForeignIdFor('master_divisions', 'division_id');
                $table->dropColumn('division_id');
            }
        });
    }
};
