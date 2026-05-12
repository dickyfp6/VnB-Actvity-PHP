<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing career stage configurations that are expected by Employee::mapLevelToCareerStage()
        // but not yet present in vnb_framework_stage_configs table
        
        $now = now();
        
        $existingStages = DB::table('vnb_framework_stage_configs')
            ->pluck('career_stage')
            ->toArray();
        
        $stagesToAdd = [
            [
                'career_stage' => 'manage_self_non_staff',
                'label' => 'Manage Self (Non-Staff)',
                'max_integrations' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'career_stage' => 'manage_self_staff',
                'label' => 'Manage Self (Staff)',
                'max_integrations' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'career_stage' => 'manage_others',
                'label' => 'Manage Other',
                'max_integrations' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'career_stage' => 'manage_function',
                'label' => 'Manage Function',
                'max_integrations' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        
        foreach ($stagesToAdd as $stage) {
            if (!in_array($stage['career_stage'], $existingStages)) {
                DB::table('vnb_framework_stage_configs')->insert($stage);
            }
        }
    }

    public function down(): void
    {
        // Remove the stages we added
        DB::table('vnb_framework_stage_configs')
            ->whereIn('career_stage', [
                'manage_self_non_staff',
                'manage_self_staff',
                'manage_others',
                'manage_function',
            ])
            ->delete();
    }
};
