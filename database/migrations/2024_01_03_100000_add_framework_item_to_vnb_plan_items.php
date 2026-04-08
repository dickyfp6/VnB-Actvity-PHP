<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This migration is deprecated - framework_item_id FK moved to 2026_04_08_000001_add_framework_foreign_key_to_vnb_plan_items.php
     * Keeping this for backward compatibility with existing deployment history
     */
    public function up(): void
    {
        // Framework foreign key is now handled by newer migration to avoid ordering issues
    }

    public function down(): void
    {
        // No changes to roll back
    }
};
