<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use App\Models\MasterDivision;
use App\Models\MasterLevel;
use App\Models\MasterEmployeeStatus;
use App\Models\MasterCompany;
use App\Models\MasterPlacement;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DummyDashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates realistic dummy data for PCX/Manager dashboards (75 employees)
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $managers = Manager::all();
        $divisions = MasterDivision::all();
        $departments = MasterDepartment::all();
        $positions = MasterPosition::all();
        $levels = MasterLevel::all();
        $statuses = MasterEmployeeStatus::all();
        $companies = MasterCompany::all();
        $placements = MasterPlacement::all();
        
        if ($managers->isEmpty() || $divisions->isEmpty() || $departments->isEmpty() || $positions->isEmpty() || $levels->isEmpty() || $statuses->isEmpty() || $companies->isEmpty() || $placements->isEmpty()) {
            $this->command->warn('⚠️  Insufficient master data. Ensure all seeders ran first.');
            return;
        }

        $this->command->info('🔄 Generating 75 dummy employee employees...');

        $lastEmpId = Employee::max('id') ?? 0;
        
        // Distribute employees across departments (75 employees / 39 departments ≈ 2 per dept)
        $deptList = $departments->toArray();
        $managerList = $managers->toArray();
        $managerCount = count($managerList);
        $deptId = 0;

        // Generate 75 employees with REQUIRED functional manager + OPTIONAL operational manager
        // Logic: manager_functional_id WAJIB ada, manager_operational_id OPSIONAL (bisa null)
        for ($i = 0; $i < 75; $i++) {
            $firstName = $faker->firstName();
            $empNumber = 'EMP' . str_pad($lastEmpId + $i + 1, 5, '0', STR_PAD_LEFT);

            $dateJoined = now()->subDays(rand(30, 270));
            $inductionDate = $dateJoined->clone()->addDays(rand(1, 7));
            $vnbStartDate = $inductionDate->clone();
            $vnbEndDate = $vnbStartDate->clone()->addDays(90);

            // Round-robin distribution across departments
            $currentDept = $deptList[$deptId % count($deptList)];
            $deptId++;

            // Manager assignment logic:
            // - Functional manager: ALWAYS required
            // - Operational manager: 60% has it, 40% null (optional)
            
            $functionalManagerIdx = $i % $managerCount;
            $functionalManagerId = $managerList[$functionalManagerIdx]['id'];
            
            // 60% chance to have operational manager
            $hasOperationalManager = (rand(1, 100) <= 60);
            $operationalManagerId = null;
            
            if ($hasOperationalManager) {
                // Pick a DIFFERENT manager than functional
                $operationalManagerIdx = ($functionalManagerIdx + 1 + rand(0, $managerCount - 2)) % $managerCount;
                $operationalManagerId = $managerList[$operationalManagerIdx]['id'];
            }

            Employee::create([
                'employee_number' => $empNumber,
                'name' => $firstName . ' ' . $faker->lastName(),
                'email' => strtolower($firstName) . '.emp' . $i . '@wismilak.com',
                'whatsapp' => '62' . str_pad(rand(800000000, 899999999), 9, '0', STR_PAD_LEFT),
                'date_joined' => $dateJoined,
                'induction_date' => $inductionDate,
                'company' => $companies->random()->name,
                'division_id' => $currentDept['division_id'],
                'department_id' => $currentDept['id'],
                'position_id' => $positions->random()->id,
                'employee_status' => $statuses->random()->name,
                'manager_functional_id' => $functionalManagerId,
                'manager_operational_id' => $operationalManagerId,
                'placement' => $placements->random()->name,
                'level' => $levels->random()->id,
                'vnb_period_start' => $vnbStartDate,
                'vnb_period_end' => $vnbEndDate,
                'vnb_status' => 'active',
                'status' => 'Aktif',
                'notes' => 'Dummy dashboard test data - ' . $currentDept['name'],
            ]);

            if (($i + 1) % 15 === 0) {
                $this->command->line('✓ Generated ' . ($i + 1) . ' employees...');
            }
        }

        // ========== AUTO-POPULATE CAREER STAGE ==========
        $this->command->line('🔄 Populating career stages for all employees...');
        $this->populateCareerStages();

        $this->command->info('✅ Dummy dashboard data created successfully!');
        $this->command->info('📊 75 employee employees distributed across ' . $departments->count() . ' departments');
        $this->command->info('🏢 Manager structure:');
        $this->command->info('   - ALL employees: Functional manager (REQUIRED)');
        $this->command->info('   - ~60% employees: Also have Operational manager (different from functional)');
        $this->command->info('   - ~40% employees: Operational manager = null (optional)');
    }

    private function populateCareerStages(): void
    {
        $employees = Employee::all();
        $totalProcessed = 0;
        $totalUpdated = 0;

        foreach ($employees as $employee) {
            $employee->load('position');
            $careerStage = $employee->getCareerStage();
            if ($careerStage && $employee->career_stage !== $careerStage) {
                $employee->career_stage = $careerStage;
                $employee->save();
                $totalUpdated++;
            }
            $totalProcessed++;
        }

        $this->command->line("✓ Career stages populated: {$totalUpdated} updated out of {$totalProcessed} total employees");
    }
}
