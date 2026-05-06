<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncEmployeesSeeder extends Seeder
{
    /**
     * Seed employees table from sync_source_employees data.
     * This seeder transforms HRIS/HRMS source data into the employee records.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('employees')->delete();
        DB::table('managers')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $now = now();
        $sourceEmployees = DB::table('sync_source_employees')->get();

        foreach ($sourceEmployees as $sourceEmp) {
            $divisionId = $this->getDivisionId($sourceEmp->division);
            $departmentId = $this->getDepartmentId($divisionId, $sourceEmp->department);
            $positionId = $this->getPositionId($sourceEmp->position);

            // Insert into employees table
            DB::table('employees')->insert([
                'employee_number' => $sourceEmp->employee_number,
                'name' => $sourceEmp->name,
                'email' => $sourceEmp->email,
                'whatsapp' => $sourceEmp->whatsapp,
                'date_joined' => $sourceEmp->date_joined,
                'company' => $sourceEmp->company,
                'division_id' => $divisionId,
                'department_id' => $departmentId,
                'position_id' => $positionId,
                'placement' => $sourceEmp->placement,
                'level' => $sourceEmp->level,
                'employee_status' => $sourceEmp->employee_status,
                'status' => $sourceEmp->status ?? 'Aktif',
                'manager_functional' => $sourceEmp->manager_functional,
                'manager_operational' => $sourceEmp->manager_operational,
                'career_stage' => $this->getCareerStageForLevel($sourceEmp->level),
                'vnb_status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // If employee is manager level, also insert into managers table
            if ($this->isManagerLevel($sourceEmp->level)) {
                DB::table('managers')->insert([
                    'name' => $sourceEmp->name,
                    'email' => $sourceEmp->email,
                    'employee_number' => $sourceEmp->employee_number,
                    'company' => $sourceEmp->company,
                    'division_id' => $divisionId,
                    'department_id' => $departmentId,
                    'status' => 'active',
                    'user_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('✅ Employees synchronized from source data:');
        $this->command->info('   - Total: ' . DB::table('employees')->count() . ' employees');
        $this->command->info('   - Managers: ' . DB::table('managers')->count());
    }

    private function getDivisionId(?string $divisionName): ?int
    {
        if (!$divisionName) {
            return null;
        }
        return DB::table('master_divisions')->where('name', $divisionName)->value('id');
    }

    private function getDepartmentId(?int $divisionId, ?string $departmentName): ?int
    {
        if (!$divisionId || !$departmentName) {
            return null;
        }
        return DB::table('master_departments')
            ->where('division_id', $divisionId)
            ->where('name', $departmentName)
            ->value('id');
    }

    private function getPositionId(?string $positionName): ?int
    {
        if (!$positionName) {
            return null;
        }
        return DB::table('master_positions')->where('name', $positionName)->value('id');
    }

    private function getCareerStageForLevel(string $level): string
    {
        return match (strtolower($level)) {
            'direktur' => 'Manage Function',
            'manager' => 'Manage Manager (Direktur)',
            'supervisor' => 'Manage Self (Staff dan Supervisor)',
            'staff' => 'Manage Self (Staff dan Supervisor)',
            'harian', 'mingguan', 'borongan' => 'Manage Self (OS)',
            default => 'Manage Self (Staff dan Supervisor)',
        };
    }

    private function isManagerLevel(string $level): bool
    {
        return in_array(strtolower($level), ['direktur', 'manager']);
    }
}
