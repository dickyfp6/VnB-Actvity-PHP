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
            // --- DIVISION: GENERAL ---
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP1001',
                'name' => 'Direktur Utama',
                'date_joined' => '2018-01-02',
                'email' => 'direktur@wiscore.id',
                'whatsapp' => '081234501001',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'General',
                'department' => 'General',
                'position' => 'Direktur Utama',
                'placement' => 'Surabaya',
                'level' => 'Director',
                'employee_status' => 'PKWTT',
            ],

            // --- DIVISION: HUMAN RESOURCE ---
            // GM (HR) - PT Wismilak (General Dept)
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP1004',
                'name' => 'Manager User',
                'date_joined' => '2020-02-17',
                'email' => 'manager@wiscore.id',
                'whatsapp' => '081234501004',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Human Resource',
                'department' => 'General',
                'position' => 'General Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            // Dept: People & Culture Excellence (PCX) - PT Gawih Djaja
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP1002',
                'name' => 'PCX Manager',
                'date_joined' => '2021-07-12',
                'email' => 'pcx@wiscore.id',
                'whatsapp' => '081234501002',
                'company' => 'PT Gawih Djaja',
                'division' => 'Human Resource',
                'department' => 'People, Culture and Experiences',
                'position' => 'Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2015',
                'name' => 'Nabila Rahmawati',
                'date_joined' => '2021-06-14',
                'email' => 'pcx.specialist@wiscore.id',
                'whatsapp' => '081234501021',
                'company' => 'PT Gawih Djaja',
                'division' => 'Human Resource',
                'department' => 'People, Culture and Experiences',
                'position' => 'Staf/Supervisor',
                'placement' => 'Surabaya',
                'level' => 'Supervisor',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP1003',
                'name' => 'Intercomm User',
                'date_joined' => '2021-07-12',
                'email' => 'intercomm@wiscore.id',
                'whatsapp' => '081234501003',
                'company' => 'PT Gawih Djaja',
                'division' => 'Human Resource',
                'department' => 'People, Culture and Experiences',
                'position' => 'Internal Communication Specialist',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],
            // Dept: Recruitment - PT Gawih Djaja
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2004',
                'name' => 'Tasya Handayani',
                'date_joined' => '2021-10-11',
                'email' => 'recruitment.manager@wiscore.id',
                'whatsapp' => '081234501010',
                'company' => 'PT Gawih Djaja',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2011',
                'name' => 'Intan Maharani',
                'date_joined' => '2022-03-15',
                'email' => 'recruitment.staff@wiscore.id',
                'whatsapp' => '081234501017',
                'company' => 'PT Gawih Djaja',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Staf/Supervisor',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2010',
                'name' => 'Yusuf Hidayat',
                'date_joined' => '2022-02-21',
                'email' => 'recruitment.staff.2@wiscore.id',
                'whatsapp' => '081234501016',
                'company' => 'PT Gawih Djaja',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Learning & Development Staff',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
            ],
            // Dept: C&B and HRIS - PT Gelora Djaja
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2002',
                'name' => 'Rina Marlina',
                'date_joined' => '2021-08-03',
                'email' => 'cnb-hris.manager@wiscore.id',
                'whatsapp' => '081234501008',
                'company' => 'PT Gelora Djaja',
                'division' => 'Human Resource',
                'department' => 'C&B and HRIS',
                'position' => 'Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2003',
                'name' => 'Agung Prasetyo',
                'date_joined' => '2021-09-14',
                'email' => 'pod.manager@wiscore.id',
                'whatsapp' => '081234501009',
                'company' => 'PT Gelora Djaja',
                'division' => 'Human Resource',
                'department' => 'C&B and HRIS',
                'position' => 'Staff',
                'placement' => 'Surabaya',
                'level' => 'Staf/Supervisor',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2009',
                'name' => 'Maya Dwi Lestari',
                'date_joined' => '2022-01-18',
                'email' => 'cnb.staff@wiscore.id',
                'whatsapp' => '081234501015',
                'company' => 'PT Gelora Djaja',
                'division' => 'Human Resource',
                'department' => 'C&B and HRIS',
                'position' => 'Payroll & Benefit Officer',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],

            // --- DIVISION: INFORMATION TECHNOLOGY ---
            // GM (IT) - PT Wismilak (General Dept)
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2001',
                'name' => 'Fajar Nugroho',
                'date_joined' => '2020-03-09',
                'email' => 'it.gm@wiscore.id',
                'whatsapp' => '081234501007',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Information Technology',
                'department' => 'General',
                'position' => 'General Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            // Dept: Technical Support - PT Gawih Djaja
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2005',
                'name' => 'Rizal Maulana',
                'date_joined' => '2022-01-05',
                'email' => 'techsupport.manager@wiscore.id',
                'whatsapp' => '081234501011',
                'company' => 'PT Gawih Djaja',
                'division' => 'Information Technology',
                'department' => 'Technical Support',
                'position' => 'Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2012',
                'name' => 'Deni Kurniawan',
                'date_joined' => '2022-05-11',
                'email' => 'techsupport.staff@wiscore.id',
                'whatsapp' => '081234501018',
                'company' => 'PT Gawih Djaja',
                'division' => 'Information Technology',
                'department' => 'Technical Support',
                'position' => 'Staf/Supervisor',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
            ],
            [
                'source_system' => 'HRMS',
                'employee_number' => 'OUTSRC001',
                'name' => 'Mardi Susanto',
                'date_joined' => '2023-01-10',
                'email' => 'mardi.susanto@wiscore.id',
                'whatsapp' => '081234601001',
                'company' => 'PT Outsourcing (Khusus OS)',
                'division' => 'Information Technology',
                'department' => 'Technical Support',
                'position' => 'Staff Teknisi Jaringan Area Harian',
                'placement' => 'Surabaya',
                'level' => 'Harian',
                'employee_status' => 'OS',
            ],
            // Dept: Webapp Dev - PT Gawih Djaja
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2006',
                'name' => 'Nanda Saputra',
                'date_joined' => '2022-02-07',
                'email' => 'webapp.manager@wiscore.id',
                'whatsapp' => '081234501012',
                'company' => 'PT Gawih Djaja',
                'division' => 'Information Technology',
                'department' => 'Webapp Dev',
                'position' => 'Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP1006',
                'name' => 'Developer',
                'date_joined' => '2022-09-05',
                'email' => 'dev@wiscore.id',
                'whatsapp' => '081234501006',
                'company' => 'PT Gawih Djaja',
                'division' => 'Information Technology',
                'department' => 'Webapp Dev',
                'position' => 'Staf/Supervisor',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP1005',
                'name' => 'Employee User',
                'date_joined' => '2024-01-10',
                'email' => 'employee@wiscore.id',
                'whatsapp' => '081234501005',
                'company' => 'PT Gawih Djaja',
                'division' => 'Information Technology',
                'department' => 'Webapp Dev',
                'position' => 'Staff Web App Junior Developer',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],
            // Dept: SAP - PT Gelora Djaja
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2007',
                'name' => 'Putri Maharani',
                'date_joined' => '2022-03-14',
                'email' => 'sap.manager@wiscore.id',
                'whatsapp' => '081234501013',
                'company' => 'PT Gelora Djaja',
                'division' => 'Information Technology',
                'department' => 'SAP',
                'position' => 'Manager',
                'placement' => 'Surabaya',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2013',
                'name' => 'Ardiansyah Putra',
                'date_joined' => '2022-06-20',
                'email' => 'sap.staff@wiscore.id',
                'whatsapp' => '081234501019',
                'company' => 'PT Gelora Djaja',
                'division' => 'Information Technology',
                'department' => 'SAP',
                'position' => 'Staf/Supervisor',
                'placement' => 'Surabaya',
                'level' => 'Supervisor',
                'employee_status' => 'PKWTT',
            ],
            [
                'source_system' => 'HRIS',
                'employee_number' => 'EMP2008',
                'name' => 'Bima Adityawan',
                'date_joined' => '2022-04-11',
                'email' => 'sap.staff.2@wiscore.id',
                'whatsapp' => '081234501014',
                'company' => 'PT Gelora Djaja',
                'division' => 'Information Technology',
                'department' => 'SAP',
                'position' => 'SAP Support Consultant',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],
        ];

        $rows = array_map(function (array $row): array {
            $row['status'] = 'Aktif';

            [$managerFunctional, $managerOperational] = $this->resolveDemoManagerNames(
                (string) ($row['division'] ?? ''),
                (string) ($row['department'] ?? '')
            );

            $row['manager_functional'] = $managerFunctional;
            $row['manager_operational'] = $managerOperational;

            return $row;
        }, $rows);

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

        // Manual overrides if needed
    }

    private function resolveDemoManagerNames(string $divisionName, string $departmentName): array
    {
        $divisionKey = mb_strtolower(trim($divisionName));
        $departmentKey = mb_strtolower(trim($departmentName));

        $assignment = [
            'human resource' => [
                'functional' => 'Direktur Utama',
                'default_operational' => 'Manager User',
                'departments' => [
                    'general' => 'Manager User',
                    'people, culture and experiences' => 'PCX Manager',
                    'c&b and hris' => 'Rina Marlina',
                    'recruitment' => 'Tasya Handayani',
                ],
            ],
            'information technology' => [
                'functional' => 'Fajar Nugroho',
                'default_operational' => 'Fajar Nugroho',
                'departments' => [
                    'general' => 'Fajar Nugroho',
                    'technical support' => 'Rizal Maulana',
                    'webapp dev' => 'Nanda Saputra',
                    'sap' => 'Putri Maharani',
                ],
            ],
        ];

        $defaultFunctional = 'Direktur Utama';
        $defaultOperational = $defaultFunctional;

        $divisionConfig = $assignment[$divisionKey] ?? null;
        if (!$divisionConfig) {
            return [$defaultFunctional, $defaultOperational];
        }

        $functional = $divisionConfig['functional'] ?? $defaultFunctional;
        $operational = $divisionConfig['departments'][$departmentKey] ?? ($divisionConfig['default_operational'] ?? $functional);

        return [$functional, $operational];
    }
}
