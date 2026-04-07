<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove framework items with individual phases (1, 2, 3)
        // Keep only range phases (1-3, 4-6, 6+)
        DB::table('vnb_framework_items')
            ->whereIn('phase', ['1', '2', '3'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed as we're deleting data
        // Data would need to be restored from backup
    }
};
