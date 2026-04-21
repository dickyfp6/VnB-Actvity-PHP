<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VnbActivityAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

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
                'name' => 'Direktur Utama',
                'email' => 'direktur@vnb.id',
                'phone' => null,
                'roles' => ['direktur_utama']
            ],
            [
                'name' => 'PCX Manager',
                'email' => 'pcx@vnb.id',
                'phone' => null,
                'roles' => ['pcx_manager', 'manager']
            ],
            [
                'name' => 'Intercomm User',
                'email' => 'intercomm@vnb.id',
                'phone' => null,
                'roles' => ['intercomm']
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@vnb.id',
                'phone' => null,
                'roles' => ['manager']
            ],
            [
                'name' => 'Employee',
                'email' => 'employee@vnb.id',
                'phone' => '082123456789',
                'roles' => ['employee']
            ],
            [
                'name' => 'Dicky Febri Primadhani',
                'email' => 'dicky@vnb.id',
                'phone' => null,
                'roles' => ['employee']
            ],
            [
                'name' => 'Ahnaf Fathan',
                'email' => 'fathan@vnb.id',
                'phone' => '081234567890',
                'roles' => ['employee']
            ],
            [
                'name' => 'Regina Dwi',
                'email' => 'rere@vnb.id',
                'phone' => '082123456788',
                'roles' => ['employee']
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

        $assignedBy = User::where('email', 'intercomm@vnb.id')->first();
        User::role('employee')->get()->each(function (User $user) use ($assignedBy): void {
            VnbActivityAssignment::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'assigned_by_user_id' => $assignedBy?->id,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'revoked_at' => null,
                ]
            );
        });

        // 3. FINALLY: Seed all other data
        $this->call([
            EmployeeAndManagerSeeder::class,
            VnbPeriodSeeder::class,
            VnbFrameworkSeeder::class,
            DummyDashboardDataSeeder::class,
            SampleVnbPlansSeeder::class,  // Create sample plans for testing
        ]);

        // 4. Auto-populate deliverables for all plan items
        $this->command->info('🔄 Auto-populating deliverables...');
        Artisan::call('vnb:populate-deliverables');
    }
}
