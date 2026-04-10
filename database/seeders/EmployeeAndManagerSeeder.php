<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeAndManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        Employee::truncate();
        Manager::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ========== MANAGERS ==========
        // Managers ter-link ke users dengan role 'manager'
        
        // Get actual user IDs from database
        $managerUser = User::where('email', 'manager@vnb.id')->first();
        $dickyUser = User::where('email', 'dicky@vnb.id')->first();
        $viqiUser = User::where('email', 'viqi@vnb.id')->firstOrCreate(
            ['email' => 'viqi@vnb.id'],
            [
                'name' => 'Viqi Alvanto',
                'password' => bcrypt('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        // Assign manager role to Viqi if it doesn't have it
        if ($viqiUser && !$viqiUser->hasRole('manager')) {
            $viqiUser->assignRole('manager');
        }

        $managers = [
            [
                'name' => 'Manager',
                'email' => 'manager@vnb.id',
                'employee_number' => '5026221022',
                'company' => 'PT Gawih Djaja',
                'division' => 'Finance & Business Support',
                'status' => 'active',
                'user_id' => $managerUser?->id,
            ],
            [
                'name' => 'Dicky Febri Primadhani',
                'email' => 'dicky@vnb.id',
                'employee_number' => '5026221036',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Strategic Research & Development (R&D)',
                'status' => 'active',
                'user_id' => $dickyUser?->id,
            ],
            [
                'name' => 'Viqi Alvanto',
                'email' => 'viqi@vnb.id',
                'employee_number' => '5026221001',
                'company' => 'PT Gelora Djaja',
                'division' => 'Supply Chain Management (SCM)',
                'status' => 'active',
                'user_id' => $viqiUser?->id,
            ],
        ];

        foreach ($managers as $managerData) {
            if ($managerData['user_id']) {  // Only create if user exists
                Manager::firstOrCreate(
                    ['email' => $managerData['email']],
                    $managerData
                );
            }
        }

        // ========== EMPLOYEES (Employees) ==========
        // Employees ter-link ke users dengan role 'employee'
        // Get all managers for assignment
        $allManagers = Manager::all();
        
        $employees = [
            [
                'employee_number' => '5026221011',
                'name' => 'Employee',
                'email' => 'employee@vnb.local',
                'whatsapp' => '082123456789',
                'date_joined' => '2026-04-01',
                'induction_date' => '2026-04-07',
                'company' => 'PT Gawih Djaja',
                'division_id' => 1,
                'department_id' => 1,
                'position_id' => 1,
                'placement' => 'Bengkulu',
                'level' => 1,
                'employee_status' => 'OS',
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'manager_functional_id' => $allManagers->count() > 0 ? $allManagers[0]->id : 1,
                'manager_operational_id' => null,  // Opsional: hanya functional manager
            ],
            [
                'employee_number' => '5026221078',
                'name' => 'Ahnaf Fathan',
                'email' => 'ahnaf@vnb.id',
                'whatsapp' => '081234567890',
                'date_joined' => '2026-04-01',
                'induction_date' => '2026-04-07',
                'company' => 'PT Gelora Djaja',
                'division_id' => 1,
                'department_id' => 1,
                'position_id' => 1,
                'placement' => 'Bandung',
                'level' => 1,
                'employee_status' => 'PKWTT',
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'manager_functional_id' => $allManagers->count() > 1 ? $allManagers[1]->id : 1,
                'manager_operational_id' => $allManagers->count() > 2 ? $allManagers[2]->id : null,  // Berbeda manager
            ],
            [
                'employee_number' => '5026221063',
                'name' => 'Regina Dwi',
                'email' => 'rere@vnb.id',
                'whatsapp' => '082123456788',
                'date_joined' => '2026-04-01',
                'induction_date' => '2026-04-07',
                'company' => 'PT Wismilak Inti Makmur',
                'division_id' => 1,
                'department_id' => 1,
                'position_id' => 1,
                'placement' => 'Banjarmasin',
                'level' => 1,
                'employee_status' => 'PKWTT',
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'manager_functional_id' => $allManagers->count() > 2 ? $allManagers[2]->id : 1,
                'manager_operational_id' => null,  // Opsional: hanya functional manager
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        // ========== AUTO-POPULATE CAREER STAGE ==========
        // After creating employees, populate career_stage based on position
        $this->populateCareerStages();

        echo "Employee and Manager seeder completed:\n";
        echo "- Managers: " . Manager::count() . "\n";
        echo "- Core Employees: " . Employee::where('id', '<=', 3)->count() . "\n";
        echo "- Total: " . Manager::count() . " managers, " . Employee::count() . " employees\n";
    }

    private function populateCareerStages(): void
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            $employee->load('position');
            $careerStage = $employee->getCareerStage();
            if ($careerStage) {
                $employee->career_stage = $careerStage;
                $employee->save();
            }
        }
    }
}
