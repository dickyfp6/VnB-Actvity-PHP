<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
    * Refreshes seeded data from scratch while preserving the core demo credentials.
     */
    public function run(): void
    {
        $this->resetSeededData();

        // 1. FIRST: Create roles, permissions, and master data
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
            SyncSourceEmployeesSeeder::class,
            EmployeeHierarchySeeder::class,
        ]);

        // 2. Recreate the six core demo credentials
        // One user may hold multiple roles; the demo accounts below reflect that access matrix.
        $testUsers = [
            [
                'name' => 'Direktur Utama',
                'email' => 'EMP1001',
                'roles' => ['direktur_utama']
            ],
            [
                'name' => 'PCX Manager',
                'email' => 'EMP1002',
                'roles' => ['pcx_manager', 'manager', 'employee']
            ],
            [
                'name' => 'Intercomm',
                'email' => 'EMP1003',
                'roles' => ['intercomm', 'employee']
            ],
            [
                'name' => 'Manager',
                'email' => 'EMP1004',
                'roles' => ['manager', 'employee']
            ],
            [
                'name' => 'Employee',
                'email' => 'EMP1005',
                'roles' => ['employee']
            ],
            [
                'name' => 'Developer',
                'email' => 'EMP1006',
                'roles' => ['direktur_utama', 'pcx_manager', 'intercomm', 'manager', 'employee']
            ],
        ];

        foreach ($testUsers as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'phone' => null,
                    'employee_id' => null,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles($roles);
        }

        $this->command->info('✅ Seeding completed - clean database with basic structure ready.');
    }

    private function resetSeededData(): void
    {
        $preservedEmails = [
            'EMP1001',
            'EMP1002',
            'EMP1003',
            'EMP1004',
            'EMP1005',
            'EMP1006',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('model_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('permissions')->delete();
        DB::table('roles')->delete();
        DB::table('sync_source_employees')->delete();
        DB::table('employees')->delete();
        DB::table('managers')->delete();
        DB::table('personal_access_tokens')->delete();
        DB::table('users')->whereNotIn('email', $preservedEmails)->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
