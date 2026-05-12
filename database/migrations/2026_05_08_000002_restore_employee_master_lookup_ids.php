<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('master_divisions')) {
            DB::table('master_divisions')->updateOrInsert(
                ['id' => 9],
                [
                    'name' => 'FAT',
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]
            );
        }

        if (Schema::hasTable('master_departments')) {
            $departments = [
                32 => ['name' => 'People, Culture and Experiences', 'division_id' => 2],
                33 => ['name' => 'C&B and HRIS', 'division_id' => 2],
                34 => ['name' => 'Recruitment', 'division_id' => 2],
                35 => ['name' => 'Webapp Dev', 'division_id' => 3],
                36 => ['name' => 'Technical Support', 'division_id' => 3],
                37 => ['name' => 'IT - SAP', 'division_id' => 3],
            ];

            foreach ($departments as $id => $department) {
                DB::table('master_departments')->updateOrInsert(
                    ['id' => $id],
                    [
                        'division_id' => $department['division_id'],
                        'name' => $department['name'],
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ]
                );
            }
        }

        if (Schema::hasTable('master_positions')) {
            $positions = [
                6 => 'Webapp Developer Manager',
                7 => 'HR Bussiness Partner Staf',
                8 => 'C&B and HRIS Manager',
                9 => 'Employee Assurance Supervisor',
                10 => 'HRIS Maintenance Staff',
                11 => 'Recruitment Manager',
                12 => 'Regional Supervisor',
                13 => 'Recruitment Staff',
                14 => 'TS Manager',
                15 => 'Technical Support',
                16 => 'SAP Manager',
                17 => 'SAP Production',
                18 => 'HRGA Specialist',
                19 => 'Sekretaris HR',
                20 => 'Network and Hardware Mainteance',
                21 => 'IT Quality Audit',
                22 => 'Production Staff',
                23 => 'Cashflow Management Assisten',
                24 => 'Tax Management Staff',
            ];

            foreach ($positions as $id => $name) {
                DB::table('master_positions')->updateOrInsert(
                    ['id' => $id],
                    [
                        'name' => $name,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_positions')) {
            DB::table('master_positions')->whereBetween('id', [6, 24])->delete();
        }

        if (Schema::hasTable('master_departments')) {
            DB::table('master_departments')->whereIn('id', [32, 33, 34, 35, 36, 37])->delete();
        }

        if (Schema::hasTable('master_divisions')) {
            DB::table('master_divisions')->where('id', 9)->delete();
        }
    }
};