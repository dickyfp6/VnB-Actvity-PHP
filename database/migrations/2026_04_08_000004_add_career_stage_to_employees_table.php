<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('employees', 'career_stage')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('career_stage', 100)->nullable()->after('placement')
                      ->comment('System-computed from position: Manage Self, Manage Others, Manage Managers, Manage Function');
            });
        }

        // Auto-populate career_stage from position names for existing employees
        $this->populateCareerStages();
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('career_stage');
        });
    }

    private function populateCareerStages(): void
    {
        $careerStageMap = [
            'non-staff' => 'Manage Self (Non-Staff)',
            'staff' => 'Manage Self (Staff)',
            'supervisor' => 'Manage Self (Staff)',
            'manager' => 'Manage Others',
            'tim leader' => 'Manage Others',
            'general manager' => 'Manage Managers',
            'kepala divisi' => 'Manage Function',
            'direktur' => 'Manage Function',
        ];

        foreach ($careerStageMap as $positionKey => $careerStage) {
            DB::update("
                UPDATE employees e
                INNER JOIN master_positions mp ON e.position_id = mp.id
                SET e.career_stage = ?
                WHERE LOWER(mp.name) LIKE CONCAT('%', ?, '%')
            ", [$careerStage, strtolower($positionKey)]);
        }
    }
};
