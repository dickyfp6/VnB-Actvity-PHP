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
        $direkturUser = User::where('email', 'direktur@wiscore.id')->first();
        $pcxUser = User::where('email', 'pcx@wiscore.id')->first();
        $managerUser = User::where('email', 'manager@wiscore.id')->first();
        $viqiUser = User::where('email', 'viqi@wiscore.id')->firstOrCreate(
            ['email' => 'viqi@wiscore.id'],
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
                'name' => 'Direktur Utama',
                'email' => 'direktur@wiscore.id',
                'employee_number' => 'EMP1001',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Human Capital & Corporate Affairs (HC&CA)',
                'department' => 'General',
                'status' => 'active',
                'user_id' => $direkturUser?->id,
            ],
            [
                'name' => 'PCX Manager',
                'email' => 'pcx@wiscore.id',
                'employee_number' => 'EMP1002',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Human Capital & Corporate Affairs (HC&CA)',
                'department' => 'People & Culture Excellence (PCX)',
                'status' => 'active',
                'user_id' => $pcxUser?->id,
            ],
            [
                'name' => 'Operations Manager',
                'email' => 'ops.manager@wiscore.id',
                'employee_number' => 'OPS001',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Operations (Pusat Produksi)',
                'status' => 'active',
                'user_id' => null,
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@wiscore.id',
                'employee_number' => 'EMP1004',
                'company' => 'PT Gawih Djaja',
                'division' => 'Finance & Business Support',
                'status' => 'active',
                'user_id' => $managerUser?->id,
            ],
            [
                'name' => 'Viqi Alvanto',
                'email' => 'viqi@wiscore.id',
                'employee_number' => '5026221001',
                'company' => 'PT Gelora Djaja',
                'division' => 'Supply Chain Management (SCM)',
                'status' => 'active',
                'user_id' => $viqiUser?->id,
            ],
        ];

        // Get General department ID fallback
        $generalDept = DB::table('master_departments')->where('name', 'General')->first();
        $generalDeptId = $generalDept?->id;

        foreach ($managers as $managerData) {
            // Resolve division_id from division name
            $division = DB::table('master_divisions')
                ->where('name', $managerData['division'])
                ->first();

            if ($division) {
                $managerData['division_id'] = $division->id;
            }

            // Resolve department_id
            if (isset($managerData['department'])) {
                $dept = DB::table('master_departments')
                    ->where('name', $managerData['department'])
                    ->first();
                $managerData['department_id'] = $dept?->id;
                unset($managerData['department']);
            } else if ($generalDeptId) {
                $managerData['department_id'] = $generalDeptId;
            }

            Manager::firstOrCreate(
                ['email' => $managerData['email']],
                $managerData
            );
        }

        // Managers seeding completed
        echo "✅ Manager seeder completed:\n";
        echo "   - Managers: " . Manager::count() . "\n";
    }
}
