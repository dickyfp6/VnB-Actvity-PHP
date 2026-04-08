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
        // Create test users with proper roles
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
                'roles' => ['manager'],
                'employee_id' => null
            ],
            [
                'name' => 'New Hire',
                'email' => 'newhire@vnb.local',
                'phone' => '082123456789',
                'roles' => ['new_hire'],
                'employee_id' => 1
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
                'roles' => ['new_hire'],
                'employee_id' => 2
            ],
            [
                'name' => 'Regina Dwi',
                'email' => 'rere@vnb.local',
                'phone' => '082123456788',
                'roles' => ['new_hire'],
                'employee_id' => 3
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
                    'employee_id' => $userData['employee_id'] ?? null,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            // Assign roles
            $user->syncRoles($roles);
        }

        // Panggil semua seeders dalam order yang benar
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
            EmployeeAndManagerSeeder::class,
            VnbPeriodSeeder::class,
            VnbFrameworkSeeder::class,
            DummyDashboardDataSeeder::class,
        ]);
    }
}
