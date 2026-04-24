<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Simplified to basic structure only - no dummy data.
     */
    public function run(): void
    {
        // 1. FIRST: Create roles, permissions, and master data
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
            SyncSourceEmployeesSeeder::class,
        ]);

        // 2. Create minimal test users for role testing only
        $testUsers = [
            [
                'name' => 'Direktur Utama',
                'email' => 'direktur@vnb.id',
                'roles' => ['direktur_utama']
            ],
            [
                'name' => 'PCX Manager',
                'email' => 'pcx@vnb.id',
                'roles' => ['pcx_manager']
            ],
            [
                'name' => 'Intercomm',
                'email' => 'intercomm@vnb.id',
                'roles' => ['intercomm']
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@vnb.id',
                'roles' => ['manager']
            ],
            [
                'name' => 'Employee',
                'email' => 'employee@vnb.id',
                'roles' => ['employee']
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
}
