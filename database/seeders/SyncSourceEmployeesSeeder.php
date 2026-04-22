<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncSourceEmployeesSeeder extends Seeder
{
    /**
     * Seed dummy readable source data from HRIS and HRMS systems.
     */
    public function run(): void
    {
        $rows = [
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP0001',
                'name' => 'Dina Prameswari',
                'date_joined' => '2022-01-17',
                'email' => 'dina.prameswari@wismilak.co.id',
                'whatsapp' => '081234500001',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Human Capital',
                'department' => 'C&B and HRIS',
                'position' => 'HRIS Specialist',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP0002',
                'name' => 'Rifki Mahendra',
                'date_joined' => '2021-08-03',
                'email' => 'rifki.mahendra@wismilak.co.id',
                'whatsapp' => '081234500002',
                'company' => 'PT Gelora Djaja',
                'division' => 'Sales',
                'department' => 'Area East',
                'position' => 'Area Supervisor',
                'placement' => 'Malang',
                'level' => 'Supervisor',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP0003',
                'name' => 'Silfia Nur Aini',
                'date_joined' => '2024-03-12',
                'email' => 'silfia.nuraini@wismilak.co.id',
                'whatsapp' => '081234500003',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Technology',
                'department' => 'Product Engineering',
                'position' => 'Backend Engineer',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
            ],
            [
                'source_system' => 'HRMS',
                'employee_number' => 'OUTSRC001',
                'name' => 'Ahmad Fauzi Pratama',
                'date_joined' => '2023-06-15',
                'email' => 'ahmad.fauzi@outsourcing.co.id',
                'whatsapp' => '081234500101',
                'company' => 'PT Outsourcing Partners',
                'division' => 'Operations',
                'department' => 'Warehouse',
                'position' => 'Warehouse Staff',
                'placement' => 'Gresik',
                'level' => 'Staff',
                'employee_status' => 'OS',
            ],
            [
                'source_system' => 'HRMS',
                'employee_number' => 'OUTSRC002',
                'name' => 'Siti Nurhasanah',
                'date_joined' => '2024-01-10',
                'email' => 'siti.nurhasanah@outsourcing.co.id',
                'whatsapp' => '081234500102',
                'company' => 'PT Outsourcing Partners',
                'division' => 'Production',
                'department' => 'Line Production',
                'position' => 'Production Operator',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'OS',
            ],
        ];

        $now = now();

        foreach ($rows as $row) {
            DB::table('sync_source_employees')->updateOrInsert(
                [
                    'source_system' => $row['source_system'],
                    'employee_number' => $row['employee_number'],
                ],
                [
                    ...$row,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
