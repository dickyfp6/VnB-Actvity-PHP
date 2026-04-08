<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create roles
        $roles = [
            'admin',
            'new_hire',
            'manager',
            'pcx_manager',
            'intercomm',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        $permissions = [
            // Employee Management
            'create_employee',
            'view_employee',
            'edit_employee',
            'delete_employee',
            'import_employees',
            'cancel_vnb',
            'reset_password',

            // Planning
            'create_planning',
            'edit_planning',
            'submit_planning',
            'view_planning',
            'approve_planning',
            'reject_planning',

            // Evidence
            'upload_evidence',
            'view_evidence',
            'verify_evidence',
            'reject_evidence',

            // Dashboard & Reporting
            'view_dashboard',
            'view_reports',
            'export_reports',

            // Master Data
            'manage_master_data',

            // System
            'view_settings',
            'manage_users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        // Admin - all permissions
        Role::findByName('admin')->givePermissionTo(Permission::all());

        // New Hire
        $newHirePermissions = [
            'create_planning',
            'edit_planning',
            'submit_planning',
            'view_planning',
            'upload_evidence',
            'view_evidence',
            'view_dashboard',
        ];
        Role::findByName('new_hire')->givePermissionTo($newHirePermissions);

        // Manager
        $managerPermissions = [
            'view_employee',
            'view_planning',
            'approve_planning',
            'reject_planning',
            'view_evidence',
            'verify_evidence',
            'reject_evidence',
            'view_dashboard',
            'view_reports',
        ];
        Role::findByName('manager')->givePermissionTo($managerPermissions);

        // PCX Manager
        $pcxPermissions = [
            'create_employee',
            'view_employee',
            'edit_employee',
            'delete_employee',
            'import_employees',
            'cancel_vnb',
            'reset_password',
            'view_planning',
            'view_evidence',
            'view_dashboard',
            'view_reports',
            'export_reports',
            'manage_master_data',
        ];
        Role::findByName('pcx_manager')->givePermissionTo($pcxPermissions);

        // Intercomm
        $intercommPermissions = [
            'view_employee',
            'reset_password',
            'view_planning',
            'view_evidence',
            'view_dashboard',
            'view_reports',
        ];
        Role::findByName('intercomm')->givePermissionTo($intercommPermissions);

        // Create demo users
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@vnb.local',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@vnb.local',
                'password' => 'password',
                'role' => 'manager',
            ],
            [
                'name' => 'New Hire User',
                'email' => 'newhire@vnb.local',
                'password' => 'password',
                'role' => 'new_hire',
            ],
            [
                'name' => 'PCX Manager',
                'email' => 'pcx@vnb.local',
                'password' => 'password',
                'role' => 'pcx_manager',
            ],
            [
                'name' => 'Intercomm User',
                'email' => 'intercomm@vnb.local',
                'password' => 'password',
                'role' => 'intercomm',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt($userData['password']),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($userData['role']);
        }
    }
}
