<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vnb_framework_stage_configs', function (Blueprint $table) {
            $table->id();
            $table->string('career_stage', 50)->unique();
            $table->string('label', 120);
            $table->unsignedTinyInteger('max_integrations')->default(2);
            $table->timestamps();
        });

        Schema::create('vnb_framework_behaviours', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->timestamps();
        });

        Schema::create('vnb_framework_stage_behaviours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_config_id')->constrained('vnb_framework_stage_configs')->cascadeOnDelete();
            $table->foreignId('behaviour_id')->constrained('vnb_framework_behaviours')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['stage_config_id', 'behaviour_id'], 'vnb_stage_behaviour_unique');
        });

        Schema::create('vnb_framework_stage_position_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_config_id')->constrained('vnb_framework_stage_configs')->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('master_positions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['stage_config_id', 'position_id'], 'vnb_stage_position_unique');
            $table->unique('position_id', 'vnb_position_single_stage_unique');
        });

        Schema::create('vnb_framework_stage_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_config_id')->constrained('vnb_framework_stage_configs')->cascadeOnDelete();
            $table->unsignedSmallInteger('phase_order');
            $table->unsignedSmallInteger('duration_months');
            $table->timestamps();

            $table->unique(['stage_config_id', 'phase_order'], 'vnb_stage_phase_unique');
        });

        Schema::table('vnb_framework_items', function (Blueprint $table) {
            if (!Schema::hasColumn('vnb_framework_items', 'integrations')) {
                $table->json('integrations')->nullable()->after('integration_2');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vnb_framework_items', function (Blueprint $table) {
            if (Schema::hasColumn('vnb_framework_items', 'integrations')) {
                $table->dropColumn('integrations');
            }
        });

        Schema::dropIfExists('vnb_framework_stage_phases');
        Schema::dropIfExists('vnb_framework_stage_position_maps');
        Schema::dropIfExists('vnb_framework_stage_behaviours');
        Schema::dropIfExists('vnb_framework_behaviours');
        Schema::dropIfExists('vnb_framework_stage_configs');
    }
};
