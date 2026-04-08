<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. FIRST: Create roles and permissions
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
        ]);

        // 2. THEN: Create test users WITHOUT employee_id (will assign later after EmployeeAndManagerSeeder runs)
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@vnb.local',
                'phone' => null,
                'roles' => ['admin']
            ],
            [
                'name' => 'PCX Manager',
                'email' => 'pcx@vnb.local',
                'phone' => null,
                'roles' => ['pcx_manager']
            ],
            [
                'name' => 'Intercomm User',
                'email' => 'intercomm@vnb.local',
                'phone' => null,
                'roles' => ['intercomm']
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@vnb.local',
                'phone' => null,
                'roles' => ['manager']
            ],
            [
                'name' => 'New Hire',
                'email' => 'newhire@vnb.local',
                'phone' => '082123456789',
                'roles' => ['new_hire']
            ],
            [
                'name' => 'Dicky Febri Primadhani',
                'email' => 'dicky@vnb.id',
                'phone' => null,
                'roles' => ['new_hire']
            ],
            [
                'name' => 'Ahnaf Fathan',
                'email' => 'fathan@vnb.local',
                'phone' => '081234567890',
                'roles' => ['new_hire']
            ],
            [
                'name' => 'Regina Dwi',
                'email' => 'rere@vnb.local',
                'phone' => '082123456788',
                'roles' => ['new_hire']
            ],
        ];

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'phone' => $userData['phone'] ?? null,
                    'employee_id' => null,  // Don't assign yet
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            // Assign roles
            $user->syncRoles($roles);
        }

        // 3. FINALLY: Seed all other data
        $this->call([
            EmployeeAndManagerSeeder::class,
            VnbPeriodSeeder::class,
            VnbFrameworkSeeder::class,
            DummyDashboardDataSeeder::class,
        ]);
    }
}
