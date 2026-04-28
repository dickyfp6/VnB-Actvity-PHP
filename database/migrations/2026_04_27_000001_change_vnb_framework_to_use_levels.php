<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old position-based mapping table
        Schema::dropIfExists('vnb_framework_stage_position_maps');

        // Create new level-based mapping table
        Schema::create('vnb_framework_stage_level_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_config_id')->constrained('vnb_framework_stage_configs')->cascadeOnDelete();
            $table->foreignId('level_id')->constrained('master_levels')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['stage_config_id', 'level_id'], 'vnb_stage_level_unique');
            $table->unique('level_id', 'vnb_level_single_stage_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnb_framework_stage_level_maps');

        // Re-create old position-based mapping table
        Schema::create('vnb_framework_stage_position_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_config_id')->constrained('vnb_framework_stage_configs')->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('master_positions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['stage_config_id', 'position_id'], 'vnb_stage_position_unique');
            $table->unique('position_id', 'vnb_position_single_stage_unique');
        });
    }
};
