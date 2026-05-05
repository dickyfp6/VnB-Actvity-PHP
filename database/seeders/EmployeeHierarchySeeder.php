<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeHierarchySeeder extends Seeder
{
    /**
     * Seed the employees table with a strict 2-division hierarchy.
     */
    public function run(): void
    {
        $this->seedMinimalMasterData();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('employees')->delete();
        DB::table('managers')->delete();
        DB::statement('ALTER TABLE employees AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE managers AUTO_INCREMENT = 1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $now = now();
        $definitions = $this->employeeDefinitions();
        $managerIds = $this->seedManagers($definitions);

        foreach ($definitions as $definition) {
            $functionalNumber = $definition['manager_functional_employee_number'] ?? null;
            $operationalNumber = $definition['manager_operational_employee_number'] ?? null;

            $row = [
                'employee_number' => $definition['employee_number'],
                'name' => $definition['name'],
                'date_joined' => $definition['date_joined'],
                'company' => $definition['company'],
                'division_id' => $this->divisionId($definition['division']),
                'department_id' => $this->departmentId($definition['division'], $definition['department']),
                'position_id' => $this->positionId($definition['position']),
                'placement' => $definition['placement'],
                'level' => $definition['level'],
                'employee_status' => $definition['employee_status'],
                'status' => 'Aktif',
                'email' => $definition['email'],
                'whatsapp' => $definition['whatsapp'],
                'manager_functional_id' => $functionalNumber ? ($managerIds[$functionalNumber] ?? null) : null,
                'manager_operational_id' => $operationalNumber ? ($managerIds[$operationalNumber] ?? null) : null,
                'manager_functional' => $functionalNumber ? $this->employeeName($definitions, $functionalNumber) : null,
                'manager_operational' => $operationalNumber ? $this->employeeName($definitions, $operationalNumber) : null,
                'career_stage' => $this->careerStageForLevel($definition['level']),
                'vnb_status' => 'active',
                'vnb_period_start' => null,
                'vnb_period_end' => null,
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $employeeIds[$definition['employee_number']] = DB::table('employees')->insertGetId($row);
        }

        $this->command->info('✓ Employee hierarchy rebuilt with real names and proper data model!');
        $this->command->info('');
        $this->command->info('HRIS Employees (10-digit numbers):');
        $this->command->info('  - 1 Direktur Utama');
        $this->command->info('  - 2 General Managers (HR, IT)');
        $this->command->info('  - 4 Department Managers');
        $this->command->info('  - 13 Staff / Supervisor employees (PKWT/PKWTT)');
        $this->command->info('  - Subtotal HRIS: 20 employees');
        $this->command->info('');
        $this->command->info('OS Employees (OS + 6 digits):');
        $this->command->info('  - 2 Harian workers');
        $this->command->info('  - 2 Mingguan workers');
        $this->command->info('  - 2 Borongan workers');
        $this->command->info('  - Subtotal OS: 6 employees');
        $this->command->info('');
        $this->command->info('📊 Total: 26 employees');
        $this->command->info('📧 All emails using @wiscore.id domain');
    }

    private function seedManagers(array $definitions): array
    {
        $now = now();
        $managerIds = [];

        foreach ($definitions as $definition) {
            if (($definition['level'] ?? null) === 'Staff') {
                continue;
            }

            DB::table('managers')->updateOrInsert(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'email' => $definition['email'],
                    'employee_number' => $definition['employee_number'],
                    'company' => $definition['company'],
                    'division' => $definition['division'],
                    'division_id' => $this->divisionId($definition['division']),
                    'department_id' => $this->departmentId($definition['division'], $definition['department']),
                    'status' => 'active',
                    'user_id' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $managerId = DB::table('managers')->where('email', $definition['email'])->value('id');
            if (!$managerId) {
                throw new \RuntimeException('Manager not found after seeding: ' . $definition['employee_number']);
            }

            $managerIds[$definition['employee_number']] = (int) $managerId;
        }

        return $managerIds;
    }

    private function seedMinimalMasterData(): void
    {
        $now = now();

        DB::table('master_divisions')->updateOrInsert(
            ['name' => 'Human Resource'],
            [
                'name' => 'Human Resource',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('master_divisions')->updateOrInsert(
            ['name' => 'Information and Technology'],
            [
                'name' => 'Information and Technology',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $divisionIds = [
            'Human Resource' => DB::table('master_divisions')->where('name', 'Human Resource')->value('id'),
            'Information and Technology' => DB::table('master_divisions')->where('name', 'Information and Technology')->value('id'),
        ];

        $departments = [
            ['division' => 'Human Resource', 'code' => 'HR-GEN', 'name' => 'General'],
            ['division' => 'Human Resource', 'code' => 'HR-REC', 'name' => 'Recruitment'],
            ['division' => 'Human Resource', 'code' => 'HR-PD', 'name' => 'People Development'],
            ['division' => 'Information and Technology', 'code' => 'IT-GEN', 'name' => 'General'],
            ['division' => 'Information and Technology', 'code' => 'IT-SE', 'name' => 'Software Engineering'],
            ['division' => 'Information and Technology', 'code' => 'IT-INF', 'name' => 'Infrastructure'],
        ];

        foreach ($departments as $department) {
            DB::table('master_departments')->updateOrInsert(
                ['division_id' => $divisionIds[$department['division']], 'name' => $department['name']],
                [
                    'division_id' => $divisionIds[$department['division']],
                    'name' => $department['name'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $positions = [
            ['name' => 'Direktur Utama'],
            ['name' => 'General Manager'],
            ['name' => 'Manager'],
            ['name' => 'Staff / Supervisor'],
        ];

        foreach ($positions as $position) {
            DB::table('master_positions')->updateOrInsert(
                ['name' => $position['name']],
                [
                    'name' => $position['name'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function divisionId(string $divisionName): int
    {
        $divisionId = DB::table('master_divisions')->where('name', $divisionName)->value('id');

        if (!$divisionId) {
            throw new \RuntimeException("Division not found: {$divisionName}");
        }

        return (int) $divisionId;
    }

    private function departmentId(string $divisionName, string $departmentName): int
    {
        $divisionId = $this->divisionId($divisionName);

        $departmentId = DB::table('master_departments')
            ->where('division_id', $divisionId)
            ->where('name', $departmentName)
            ->value('id');

        if (!$departmentId) {
            throw new \RuntimeException("Department not found: {$divisionName} / {$departmentName}");
        }

        return (int) $departmentId;
    }

    private function positionId(string $positionName): int
    {
        $positionId = DB::table('master_positions')->where('name', $positionName)->value('id');

        if (!$positionId) {
            throw new \RuntimeException("Position not found: {$positionName}");
        }

        return (int) $positionId;
    }

    private function employeeName(array $definitions, string $employeeNumber): string
    {
        foreach ($definitions as $definition) {
            if ($definition['employee_number'] === $employeeNumber) {
                return $definition['name'];
            }
        }

        throw new \RuntimeException("Employee definition not found: {$employeeNumber}");
    }

    private function careerStageForLevel(string $level): string
    {
        return match ($level) {
            'Direktur' => 'Manage Function',
            'Manager' => 'Manage Manager (Direktur)',
            'Staff' => 'Manage Self (Staff dan Supervisor)',
            'Harian', 'Mingguan', 'Borongan' => 'Manage Self (OS)',
            default => 'Manage Self (Staff dan Supervisor)',
        };
    }

    private function employeeDefinitions(): array
    {
        // HRIS EMPLOYEES (10-digit numbers)
        $hrisBase = [
            'source_system' => 'HRIS',
            'date_joined' => '2024-01-01',
            'company' => 'PT Wismilak Inti Makmur',
            'placement' => 'Surabaya',
        ];

        // OS EMPLOYEES (OS + 6 digits)
        $osBase = [
            'source_system' => 'OS',
            'date_joined' => '2024-03-01',
            'company' => 'PT Wismilak Inti Makmur',
            'placement' => 'Surabaya',
            'employee_status' => 'OS',
        ];

        return [
            // ========== HRIS: DIREKTUR ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000001',
                'name' => 'Rifki Mahendra',
                'email' => 'rifki.mahendra@wiscore.id',
                'whatsapp' => '081234501001',
                'division' => 'Human Resource',
                'department' => 'General',
                'position' => 'Direktur Utama',
                'level' => 'Direktur',
                'employee_status' => 'PKWTT',
            ],

            // ========== HRIS: GENERAL MANAGERS ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000002',
                'name' => 'Dina Prameswari',
                'email' => 'dina.prameswari@wiscore.id',
                'whatsapp' => '081234501002',
                'division' => 'Human Resource',
                'department' => 'General',
                'position' => 'General Manager',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
                'manager_functional_employee_number' => '1001000001',
                'manager_operational_employee_number' => '1001000001',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000003',
                'name' => 'Aditya Ramadhan',
                'email' => 'aditya.ramadhan@wiscore.id',
                'whatsapp' => '081234501003',
                'division' => 'Information and Technology',
                'department' => 'General',
                'position' => 'General Manager',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
                'manager_functional_employee_number' => '1001000001',
                'manager_operational_employee_number' => '1001000001',
            ],

            // ========== HRIS: DEPARTMENT MANAGERS ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000004',
                'name' => 'Silfia Nur Aini',
                'email' => 'silfia.aini@wiscore.id',
                'whatsapp' => '081234501004',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Manager',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
                'manager_functional_employee_number' => '1001000001',
                'manager_operational_employee_number' => '1001000002',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000005',
                'name' => 'Nadya Paramitha',
                'email' => 'nadya.paramitha@wiscore.id',
                'whatsapp' => '081234501005',
                'division' => 'Human Resource',
                'department' => 'People Development',
                'position' => 'Manager',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
                'manager_functional_employee_number' => '1001000001',
                'manager_operational_employee_number' => '1001000002',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000006',
                'name' => 'Rangga Prakoso',
                'email' => 'rangga.prakoso@wiscore.id',
                'whatsapp' => '081234501006',
                'division' => 'Information and Technology',
                'department' => 'Software Engineering',
                'position' => 'Manager',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
                'manager_functional_employee_number' => '1001000001',
                'manager_operational_employee_number' => '1001000003',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000007',
                'name' => 'Yusuf Kurniawan',
                'email' => 'yusuf.kurniawan@wiscore.id',
                'whatsapp' => '081234501007',
                'division' => 'Information and Technology',
                'department' => 'Infrastructure',
                'position' => 'Manager',
                'level' => 'Manager',
                'employee_status' => 'PKWTT',
                'manager_functional_employee_number' => '1001000001',
                'manager_operational_employee_number' => '1001000003',
            ],

            // ========== HRIS: STAFF & SUPERVISORS (HR RECRUITMENT) ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000008',
                'name' => 'Tri Rahayu',
                'email' => 'tri.rahayu@wiscore.id',
                'whatsapp' => '081234501008',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000004',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000009',
                'name' => 'Bambang Sutrisno',
                'email' => 'bambang.sutrisno@wiscore.id',
                'whatsapp' => '081234501009',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000004',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000010',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@wiscore.id',
                'whatsapp' => '081234501010',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000004',
            ],

            // ========== HRIS: STAFF & SUPERVISORS (HR PEOPLE DEVELOPMENT) ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000011',
                'name' => 'Hendra Wijaya',
                'email' => 'hendra.wijaya@wiscore.id',
                'whatsapp' => '081234501011',
                'division' => 'Human Resource',
                'department' => 'People Development',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000005',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000012',
                'name' => 'Citra Dewi',
                'email' => 'citra.dewi@wiscore.id',
                'whatsapp' => '081234501012',
                'division' => 'Human Resource',
                'department' => 'People Development',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000005',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000013',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@wiscore.id',
                'whatsapp' => '081234501013',
                'division' => 'Human Resource',
                'department' => 'People Development',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000005',
            ],

            // ========== HRIS: STAFF & SUPERVISORS (HR GENERAL) ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000014',
                'name' => 'Rini Hermawan',
                'email' => 'rini.hermawan@wiscore.id',
                'whatsapp' => '081234501014',
                'division' => 'Human Resource',
                'department' => 'General',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000002',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000015',
                'name' => 'Andi Hidayat',
                'email' => 'andi.hidayat@wiscore.id',
                'whatsapp' => '081234501015',
                'division' => 'Human Resource',
                'department' => 'General',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000002',
                'manager_operational_employee_number' => '1001000002',
            ],

            // ========== HRIS: STAFF & SUPERVISORS (IT SOFTWARE ENGINEERING) ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000016',
                'name' => 'Rahmat Septian',
                'email' => 'rahmat.septian@wiscore.id',
                'whatsapp' => '081234501016',
                'division' => 'Information and Technology',
                'department' => 'Software Engineering',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000003',
                'manager_operational_employee_number' => '1001000006',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000017',
                'name' => 'Elisa Putri',
                'email' => 'elisa.putri@wiscore.id',
                'whatsapp' => '081234501017',
                'division' => 'Information and Technology',
                'department' => 'Software Engineering',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000003',
                'manager_operational_employee_number' => '1001000006',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000018',
                'name' => 'Fajar Yuda',
                'email' => 'fajar.yuda@wiscore.id',
                'whatsapp' => '081234501018',
                'division' => 'Information and Technology',
                'department' => 'Software Engineering',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000003',
                'manager_operational_employee_number' => '1001000006',
            ],

            // ========== HRIS: STAFF & SUPERVISORS (IT INFRASTRUCTURE) ==========
            [
                ...$hrisBase,
                'employee_number' => '1001000019',
                'name' => 'Sugeng Riyanto',
                'email' => 'sugeng.riyanto@wiscore.id',
                'whatsapp' => '081234501019',
                'division' => 'Information and Technology',
                'department' => 'Infrastructure',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000003',
                'manager_operational_employee_number' => '1001000007',
            ],
            [
                ...$hrisBase,
                'employee_number' => '1001000020',
                'name' => 'Qori Nabila',
                'email' => 'qori.nabila@wiscore.id',
                'whatsapp' => '081234501020',
                'division' => 'Information and Technology',
                'department' => 'Infrastructure',
                'position' => 'Staff / Supervisor',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
                'manager_functional_employee_number' => '1001000003',
                'manager_operational_employee_number' => '1001000007',
            ],

            // ========== OS EMPLOYEES (HARIAN, MINGGUAN, BORONGAN) ==========
            [
                ...$osBase,
                'employee_number' => 'OS000001',
                'name' => 'Mardi Susanto',
                'email' => 'mardi.susanto@wiscore.id',
                'whatsapp' => '081234601001',
                'division' => 'Human Resource',
                'department' => 'General',
                'position' => 'Staff / Supervisor',
                'level' => 'Harian',
            ],
            [
                ...$osBase,
                'employee_number' => 'OS000002',
                'name' => 'Tini Suryani',
                'email' => 'tini.suryani@wiscore.id',
                'whatsapp' => '081234601002',
                'division' => 'Human Resource',
                'department' => 'Recruitment',
                'position' => 'Staff / Supervisor',
                'level' => 'Mingguan',
            ],
            [
                ...$osBase,
                'employee_number' => 'OS000003',
                'name' => 'Harjanto Purnomo',
                'email' => 'harjanto.purnomo@wiscore.id',
                'whatsapp' => '081234601003',
                'division' => 'Information and Technology',
                'department' => 'Software Engineering',
                'position' => 'Staff / Supervisor',
                'level' => 'Borongan',
            ],
            [
                ...$osBase,
                'employee_number' => 'OS000004',
                'name' => 'Lestari Wulandari',
                'email' => 'lestari.wulandari@wiscore.id',
                'whatsapp' => '081234601004',
                'division' => 'Information and Technology',
                'department' => 'Infrastructure',
                'position' => 'Staff / Supervisor',
                'level' => 'Harian',
            ],
            [
                ...$osBase,
                'employee_number' => 'OS000005',
                'name' => 'Irwan Setiyawan',
                'email' => 'irwan.setiyawan@wiscore.id',
                'whatsapp' => '081234601005',
                'division' => 'Human Resource',
                'department' => 'People Development',
                'position' => 'Staff / Supervisor',
                'level' => 'Mingguan',
            ],
            [
                ...$osBase,
                'employee_number' => 'OS000006',
                'name' => 'Endang Mulyani',
                'email' => 'endang.mulyani@wiscore.id',
                'whatsapp' => '081234601006',
                'division' => 'Information and Technology',
                'department' => 'General',
                'position' => 'Staff / Supervisor',
                'level' => 'Borongan',
            ],
        ];
    }
}