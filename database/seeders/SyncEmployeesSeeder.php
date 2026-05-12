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
        $mapping = [
            'General' => 1,
            'Human Resource' => 2,
            'Information Technology' => 3,
            'FAT' => 9,
        ];
        return $mapping[$divisionName] ?? null;
    }

    private function getDepartmentId(?int $divisionId, ?string $departmentName): ?int
    {
        if (!$divisionId || !$departmentName) {
            return null;
        }
        $mapping = [
            'General' => 1,
            'People, Culture and Experiences' => 32,
            'C&B and HRIS' => 33,
            'Recruitment' => 34,
            'Webapp Dev' => 35,
            'Technical Support' => 36,
            'IT - SAP' => 37,
            'Factory Production' => 34,
            'Accounting Purchase' => 35,
            'Tax Record' => 36,
        ];
        return $mapping[$departmentName] ?? null;
    }

    private function getPositionId(?string $positionName): ?int
    {
        if (!$positionName) {
            return null;
        }
        $mapping = [
            'Direktur Utama' => 1,
            'Manager' => 2,
            'Internal Communication Specialist' => 3,
            'General Manager' => 4,
            'Web Junior Developer' => 5,
            'Webapp Developer Manager' => 6,
            'HR Bussiness Partner Staf' => 7,
            'C&B and HRIS Manager' => 8,
            'Employee Assurance Supervisor' => 9,
            'HRIS Maintenance Staff' => 10,
            'Recruitment Manager' => 11,
            'Regional Supervisor' => 12,
            'Recruitment Staff' => 13,
            'TS Manager' => 14,
            'Technical Support' => 15,
            'SAP Manager' => 16,
            'SAP Production' => 17,
            'HRGA Specialist' => 18,
            'Sekretaris HR' => 19,
            'Network and Hardware Mainteance' => 20,
            'IT Quality Audit' => 21,
            'Production Staff' => 22,
            'Cashflow Management Assisten' => 23,
            'Tax Management Staff' => 24,
        ];
        return $mapping[$positionName] ?? null;
    }

    private function getCareerStageForLevel(string $level): ?string
    {
        // Seeders should not assume career stage mapping. Leave null so framework UI defines mappings.
        return null;
    }

    private function isManagerLevel(string $level): bool
    {
        return in_array(strtolower($level), ['direktur', 'manager']);
    }
}
