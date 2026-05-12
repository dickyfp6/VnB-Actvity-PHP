<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'division_id')) {
                    $table->dropForeign(['division_id']);
                }

                if (Schema::hasColumn('employees', 'department_id')) {
                    $table->dropForeign(['department_id']);
                }

                if (Schema::hasColumn('employees', 'position_id')) {
                    $table->dropForeign(['position_id']);
                }
            });
        }

        if (Schema::hasTable('employee_histories')) {
            Schema::table('employee_histories', function (Blueprint $table) {
                if (Schema::hasColumn('employee_histories', 'division_id')) {
                    $table->dropForeign(['division_id']);
                }

                if (Schema::hasColumn('employee_histories', 'department_id')) {
                    $table->dropForeign(['department_id']);
                }

                if (Schema::hasColumn('employee_histories', 'position_id')) {
                    $table->dropForeign(['position_id']);
                }
            });
        }

        if (Schema::hasTable('managers')) {
            Schema::table('managers', function (Blueprint $table) {
                if (Schema::hasColumn('managers', 'division_id')) {
                    $table->dropForeign(['division_id']);
                }

                if (Schema::hasColumn('managers', 'department_id')) {
                    $table->dropForeign(['department_id']);
                }
            });
        }

        if (Schema::hasTable('vnb_framework_stage_level_maps')) {
            Schema::table('vnb_framework_stage_level_maps', function (Blueprint $table) {
                if (Schema::hasColumn('vnb_framework_stage_level_maps', 'level_id')) {
                    $table->dropForeign(['level_id']);
                }
            });
        }

        if (Schema::hasTable('vnb_framework_stage_position_maps')) {
            Schema::table('vnb_framework_stage_position_maps', function (Blueprint $table) {
                if (Schema::hasColumn('vnb_framework_stage_position_maps', 'position_id')) {
                    $table->dropForeign(['position_id']);
                }
            });
        }

        foreach ([
            'master_employee_statuses',
            'master_placements',
            'master_levels',
            'master_positions',
            'master_departments',
            'master_divisions',
            'master_companies',
        ] as $tableName) {
            Schema::dropIfExists($tableName);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Intentionally not restoring master tables.
    }
};
