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
        // Buat test users untuk development (hanya untuk testing, gunakan akun real employee untuk demo)
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@vnb.local'],
            ['name' => 'Manager User', 'email' => 'manager@vnb.local'],
            ['name' => 'Intercomm User', 'email' => 'intercomm@vnb.local'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        // Panggil seeder role & framework yang sudah kamu punya
        $this->call([
            RolePermissionSeeder::class,
            VnbFrameworkSeeder::class,
        ]);
    }
}
