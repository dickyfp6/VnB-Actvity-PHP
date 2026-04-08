<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use App\Models\MasterDivision;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DummyDashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates realistic dummy data for PCX/Manager dashboards (75 new hires)
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $managers = Manager::all();
        $divisions = MasterDivision::all();
        $departments = MasterDepartment::all();
        $positions = MasterPosition::all();
        
        if ($managers->isEmpty() || $divisions->isEmpty() || $departments->isEmpty() || $positions->isEmpty()) {
            $this->command->warn('⚠️  Insufficient master data. Ensure all seeders ran first.');
            return;
        }

        $this->command->info('🔄 Generating 75 dummy new hire employees...');

        // Valid employee status values (from master_employee_statuses)
        $validStatuses = ['OS', 'PKWTT', 'PKWT'];
        $lastEmpId = Employee::max('id') ?? 0;
        
        // Distribute employees across departments (75 employees / 39 departments ≈ 2 per dept)
        $deptCount = 0;
        $deptId = 0;
        $deptList = $departments->toArray();

        // Generate 75 employees - distribute across all departments
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

            Employee::create([
                'employee_number' => $empNumber,
                'name' => $firstName . ' ' . $faker->lastName(),
                'email' => strtolower($firstName) . '.emp' . $i . '@wismilak.com',
                'whatsapp' => '62' . str_pad(rand(800000000, 899999999), 9, '0', STR_PAD_LEFT),
                'date_joined' => $dateJoined,
                'induction_date' => $inductionDate,
                'company' => 'PT. Wismilak Inti Makmur, Tbk',
                'division_id' => $currentDept['division_id'],
                'department_id' => $currentDept['id'],
                'position_id' => $positions->random()->id,
                'employee_status' => $validStatuses[array_rand($validStatuses)],
                'manager_functional_id' => $managers->random()->id,
                'manager_operational_id' => $managers->count() > 1 ? $managers->random()->id : null,
                'placement' => 'Jakarta',
                'level' => rand(1, 5),
                'vnb_period_start' => $vnbStartDate,
                'vnb_period_end' => $vnbEndDate,
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'notes' => 'Dummy dashboard test data - ' . $currentDept['name'],
            ]);

            if (($i + 1) % 15 === 0) {
                $this->command->line('✓ Generated ' . ($i + 1) . ' employees...');
            }
        }

        $this->command->info('✅ Dummy dashboard data created successfully!');
        $this->command->info('📊 75 new hire employees distributed across ' . $departments->count() . ' departments');
        $this->command->info('🏢 Divisions: ' . $divisions->count() . ' (Operations, SCM, Commercial, HC&CA, Finance, R&D)');
    }
}
