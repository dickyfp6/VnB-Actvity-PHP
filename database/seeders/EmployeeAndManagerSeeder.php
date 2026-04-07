<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Manager;
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
        $managers = [
            [
                'name' => 'Manager',
                'email' => 'manager@vnb.local',
                'employee_number' => '5026221022',
                'company' => 'PT Gawih Djaja',
                'division' => 'Information and Technology',
                'status' => 'active',
                'user_id' => 11, // manager@vnb.local user
            ],
            [
                'name' => 'Dicky Febri Primadhani',
                'email' => 'dicky@vnb.id',
                'employee_number' => '5026221036',
                'company' => 'PT Wismilak Inti Makmur, Tbk',
                'division' => 'Information and Technology',
                'status' => 'active',
                'user_id' => 13, // dicky@vnb.id user
            ],
        ];

        foreach ($managers as $managerData) {
            Manager::create($managerData);
        }

        // ========== EMPLOYEES (New Hires) ==========
        // Employees ter-link ke users dengan role 'new_hire'
        $employees = [
            [
                'employee_number' => '5026221011',
                'name' => 'New Hire',
                'email' => 'newhire@vnb.local',
                'whatsapp' => '082123456789',
                'date_joined' => '2026-04-01',
                'induction_date' => '2026-04-07',
                'company' => 'PT Gawih Djaja',
                'division_id' => 5,
                'department_id' => 4,
                'position_id' => 2,
                'placement' => 'Bengkulu',
                'level' => 'Staff/Supervisor',
                'employee_status' => 'OS',
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'manager_functional_id' => 1,
            ],
            [
                'employee_number' => '5026221078',
                'name' => 'Ahnaf Fathan',
                'email' => 'ahnaf@vnb.id',
                'whatsapp' => '081234567890',
                'date_joined' => '2026-04-01',
                'induction_date' => '2026-04-07',
                'company' => 'PT Gelora Djaja',
                'division_id' => 6,
                'department_id' => 12,
                'position_id' => 5,
                'placement' => 'Bandung',
                'level' => 'Staff/Supervisor',
                'employee_status' => 'PKWTT',
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'manager_functional_id' => 2,
            ],
            [
                'employee_number' => '5026221063',
                'name' => 'Regina Dwi',
                'email' => 'rere@vnb.id',
                'whatsapp' => '082123456788',
                'date_joined' => '2026-04-01',
                'induction_date' => '2026-04-07',
                'company' => 'PT Wismilak Inti Makmur, Tbk',
                'division_id' => 6,
                'department_id' => 5,
                'position_id' => 3,
                'placement' => 'Banjarmasin',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'manager_functional_id' => 1,
                'manager_operational_id' => 2,
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        echo "Employee and Manager seeder completed:\n";
        echo "- Managers: " . Manager::count() . "\n";
        echo "- Employees: " . Employee::count() . "\n";
    }
}
