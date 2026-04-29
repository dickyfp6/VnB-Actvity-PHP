<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\MasterDepartment;
use App\Models\MasterDivision;
use App\Models\MasterEmployeeStatus;
use App\Models\MasterLevel;
use App\Models\MasterPosition;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TwoDivisionDemoSeeder extends Seeder
{
    public function run(): void
    {
        $company = 'PT Wismilak Inti Makmur';

        $divisionHr = MasterDivision::firstOrCreate(['name' => 'Human Resource']);
        $divisionIt = MasterDivision::firstOrCreate(['name' => 'IT']);

        $departments = [
            'hr' => [
                'General',
                'People, Culture, and Experiences',
                'C&B and HRIS',
                'People Operations and Development',
                'Recruitment',
            ],
            'it' => [
                'General',
                'Technical Support',
                'Webapp Dev',
                'SAP',
                'Infrastructure & Security',
            ],
        ];

        $masterDepartments = [];
        foreach ($departments['hr'] as $departmentName) {
            $masterDepartments['hr'][$departmentName] = MasterDepartment::firstOrCreate([
                'division_id' => $divisionHr->id,
                'name' => $departmentName,
            ]);
        }
        foreach ($departments['it'] as $departmentName) {
            $masterDepartments['it'][$departmentName] = MasterDepartment::firstOrCreate([
                'division_id' => $divisionIt->id,
                'name' => $departmentName,
            ]);
        }

        $positions = [
            'Manager',
            'Staf/Supervisor',
            'Harian',
            'Mingguan',
            'Borongan',
        ];

        $positionIds = [];
        foreach ($positions as $positionName) {
            $positionIds[$positionName] = MasterPosition::firstOrCreate(['name' => $positionName])->id;
        }

        $levels = [
            'Director',
            'General Manager',
            'Manager',
            'Staff',
            'Supervisor',
        ];

        foreach ($levels as $levelName) {
            MasterLevel::firstOrCreate(['name' => $levelName]);
        }

        foreach (['PKWTT', 'PKWT', 'OS'] as $statusName) {
            MasterEmployeeStatus::firstOrCreate(['name' => $statusName]);
        }

        $director = $this->seedManagerEmployee([
            'employee_number' => 'DIR-0001',
            'name' => 'Direktur Utama',
            'email' => 'direktur@vnb.id',
            'whatsapp' => '081234501001',
            'date_joined' => '2018-01-02',
            'induction_date' => '2018-01-02',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'General',
            'department_id' => $masterDepartments['hr']['General']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Director',
            'employee_status' => 'PKWTT',
        ], ['direktur_utama']);

        $hrGeneralManager = $this->seedManagerEmployee([
            'employee_number' => 'HR-GM-0001',
            'name' => 'Human Resource General Manager',
            'email' => 'manager@vnb.id',
            'whatsapp' => '081234501004',
            'date_joined' => '2020-02-17',
            'induction_date' => '2020-02-17',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'General',
            'department_id' => $masterDepartments['hr']['General']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'General Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $director);

        $itGeneralManager = $this->seedManagerEmployee([
            'employee_number' => 'IT-GM-0001',
            'name' => 'IT General Manager',
            'email' => 'it.gm@vnb.id',
            'whatsapp' => '081234501007',
            'date_joined' => '2020-03-09',
            'induction_date' => '2020-03-09',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'General',
            'department_id' => $masterDepartments['it']['General']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'General Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $director);

        $pcxManager = $this->seedManagerEmployee([
            'employee_number' => 'HR-PCX-0001',
            'name' => 'PCX Manager',
            'email' => 'pcx@vnb.id',
            'whatsapp' => '081234501002',
            'date_joined' => '2021-07-12',
            'induction_date' => '2021-07-12',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'People, Culture, and Experiences',
            'department_id' => $masterDepartments['hr']['People, Culture, and Experiences']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['pcx_manager', 'manager'], $hrGeneralManager, $director);

        $hrCnbManager = $this->seedManagerEmployee([
            'employee_number' => 'HR-CNB-0001',
            'name' => 'C&B and HRIS Manager',
            'email' => 'cnb-hris.manager@vnb.id',
            'whatsapp' => '081234501008',
            'date_joined' => '2021-08-03',
            'induction_date' => '2021-08-03',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'C&B and HRIS',
            'department_id' => $masterDepartments['hr']['C&B and HRIS']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $hrGeneralManager, $director);

        $hrPodManager = $this->seedManagerEmployee([
            'employee_number' => 'HR-POD-0001',
            'name' => 'POD Manager',
            'email' => 'pod.manager@vnb.id',
            'whatsapp' => '081234501009',
            'date_joined' => '2021-09-14',
            'induction_date' => '2021-09-14',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'People Operations and Development',
            'department_id' => $masterDepartments['hr']['People Operations and Development']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $hrGeneralManager, $director);

        $hrRecruitmentManager = $this->seedManagerEmployee([
            'employee_number' => 'HR-REC-0001',
            'name' => 'Recruitment Manager',
            'email' => 'recruitment.manager@vnb.id',
            'whatsapp' => '081234501010',
            'date_joined' => '2021-10-11',
            'induction_date' => '2021-10-11',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'Recruitment',
            'department_id' => $masterDepartments['hr']['Recruitment']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $hrGeneralManager, $director);

        $itTechManager = $this->seedManagerEmployee([
            'employee_number' => 'IT-TS-0001',
            'name' => 'Technical Support Manager',
            'email' => 'techsupport.manager@vnb.id',
            'whatsapp' => '081234501011',
            'date_joined' => '2022-01-05',
            'induction_date' => '2022-01-05',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'Technical Support',
            'department_id' => $masterDepartments['it']['Technical Support']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $itGeneralManager, $director);

        $itWebappManager = $this->seedManagerEmployee([
            'employee_number' => 'IT-WEB-0001',
            'name' => 'Webapp Dev Manager',
            'email' => 'webapp.manager@vnb.id',
            'whatsapp' => '081234501012',
            'date_joined' => '2022-02-07',
            'induction_date' => '2022-02-07',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'Webapp Dev',
            'department_id' => $masterDepartments['it']['Webapp Dev']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $itGeneralManager, $director);

        $itSapManager = $this->seedManagerEmployee([
            'employee_number' => 'IT-SAP-0001',
            'name' => 'SAP Manager',
            'email' => 'sap.manager@vnb.id',
            'whatsapp' => '081234501013',
            'date_joined' => '2022-03-14',
            'induction_date' => '2022-03-14',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'SAP',
            'department_id' => $masterDepartments['it']['SAP']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $itGeneralManager, $director);

        $itInfraManager = $this->seedManagerEmployee([
            'employee_number' => 'IT-INF-0001',
            'name' => 'Infrastructure Manager',
            'email' => 'infra.manager@vnb.id',
            'whatsapp' => '081234501014',
            'date_joined' => '2022-04-11',
            'induction_date' => '2022-04-11',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'Infrastructure & Security',
            'department_id' => $masterDepartments['it']['Infrastructure & Security']->id,
            'position_id' => $positionIds['Manager'],
            'placement' => 'Surabaya',
            'level' => 'Manager',
            'employee_status' => 'PKWTT',
        ], ['manager'], $itGeneralManager, $director);

        $this->seedStaffEmployee([
            'employee_number' => 'HR-PCX-1001',
            'name' => 'Intercomm User',
            'email' => 'intercomm@vnb.id',
            'whatsapp' => '081234501003',
            'date_joined' => '2021-07-12',
            'induction_date' => '2021-07-12',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'People, Culture, and Experiences',
            'department_id' => $masterDepartments['hr']['People, Culture, and Experiences']->id,
            'position_id' => $positionIds['Staf/Supervisor'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWTT',
        ], ['intercomm', 'employee'], $pcxManager, $hrGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'IT-WEB-1001',
            'name' => 'Developer',
            'email' => 'dev@vnb.id',
            'whatsapp' => '081234501006',
            'date_joined' => '2022-09-05',
            'induction_date' => '2022-09-05',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'Webapp Dev',
            'department_id' => $masterDepartments['it']['Webapp Dev']->id,
            'position_id' => $positionIds['Borongan'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWT',
        ], ['employee'], $itWebappManager, $itGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'HR-CNB-1001',
            'name' => 'C&B Staff',
            'email' => 'cnb.staff@vnb.id',
            'whatsapp' => '081234501015',
            'date_joined' => '2022-01-18',
            'induction_date' => '2022-01-18',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'C&B and HRIS',
            'department_id' => $masterDepartments['hr']['C&B and HRIS']->id,
            'position_id' => $positionIds['Staf/Supervisor'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWTT',
        ], ['employee'], $hrCnbManager, $hrGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'HR-POD-1001',
            'name' => 'POD Staff',
            'email' => 'pod.staff@vnb.id',
            'whatsapp' => '081234501016',
            'date_joined' => '2022-02-21',
            'induction_date' => '2022-02-21',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'People Operations and Development',
            'department_id' => $masterDepartments['hr']['People Operations and Development']->id,
            'position_id' => $positionIds['Mingguan'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWT',
        ], ['employee'], $hrPodManager, $hrGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'HR-REC-1001',
            'name' => 'Recruitment Staff',
            'email' => 'recruitment.staff@vnb.id',
            'whatsapp' => '081234501017',
            'date_joined' => '2022-03-15',
            'induction_date' => '2022-03-15',
            'company' => $company,
            'division_name' => 'Human Resource',
            'division_id' => $divisionHr->id,
            'department_name' => 'Recruitment',
            'department_id' => $masterDepartments['hr']['Recruitment']->id,
            'position_id' => $positionIds['Harian'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWTT',
        ], ['employee'], $hrRecruitmentManager, $hrGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'IT-TS-1001',
            'name' => 'Technical Support Staff',
            'email' => 'techsupport.staff@vnb.id',
            'whatsapp' => '081234501018',
            'date_joined' => '2022-05-11',
            'induction_date' => '2022-05-11',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'Technical Support',
            'department_id' => $masterDepartments['it']['Technical Support']->id,
            'position_id' => $positionIds['Staf/Supervisor'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWT',
        ], ['employee'], $itTechManager, $itGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'IT-SAP-1001',
            'name' => 'SAP Staff',
            'email' => 'sap.staff@vnb.id',
            'whatsapp' => '081234501019',
            'date_joined' => '2022-06-20',
            'induction_date' => '2022-06-20',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'SAP',
            'department_id' => $masterDepartments['it']['SAP']->id,
            'position_id' => $positionIds['Borongan'],
            'placement' => 'Surabaya',
            'level' => 'Supervisor',
            'employee_status' => 'PKWTT',
        ], ['employee'], $itSapManager, $itGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'IT-INF-1001',
            'name' => 'Infrastructure Staff',
            'email' => 'infra.staff@vnb.id',
            'whatsapp' => '081234501020',
            'date_joined' => '2022-07-04',
            'induction_date' => '2022-07-04',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'Infrastructure & Security',
            'department_id' => $masterDepartments['it']['Infrastructure & Security']->id,
            'position_id' => $positionIds['Mingguan'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWT',
        ], ['employee'], $itInfraManager, $itGeneralManager);

        $this->seedStaffEmployee([
            'employee_number' => 'IT-WEB-1002',
            'name' => 'Employee User',
            'email' => 'employee@vnb.id',
            'whatsapp' => '081234501005',
            'date_joined' => '2024-01-10',
            'induction_date' => '2024-01-10',
            'company' => $company,
            'division_name' => 'IT',
            'division_id' => $divisionIt->id,
            'department_name' => 'Webapp Dev',
            'department_id' => $masterDepartments['it']['Webapp Dev']->id,
            'position_id' => $positionIds['Harian'],
            'placement' => 'Surabaya',
            'level' => 'Staff',
            'employee_status' => 'PKWTT',
        ], ['employee'], $itWebappManager, $itGeneralManager);
    }

    private function seedManagerEmployee(array $data, array $roles, ?Manager $functionalManager = null, ?Manager $operationalManager = null): Manager
    {
        $user = User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make('password'),
                'phone' => $data['whatsapp'] ?? null,
                'status' => 'active',
                'employee_id' => null,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles($roles);

        $manager = Manager::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'employee_number' => $data['employee_number'],
                'company' => $data['company'],
                'division' => $data['division_name'],
                'status' => 'active',
                'user_id' => $user->id,
            ]
        );

        $employee = Employee::query()
            ->where('email', $data['email'])
            ->orWhere('employee_number', $data['employee_number'])
            ->first();

        $employeePayload = [
            'employee_number' => $data['employee_number'],
            'name' => $data['name'],
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'date_joined' => $data['date_joined'],
            'induction_date' => $data['induction_date'] ?? $data['date_joined'],
            'company' => $data['company'],
            'division_id' => $data['division_id'],
            'department_id' => $data['department_id'],
            'position_id' => $data['position_id'],
            'placement' => $data['placement'],
            'level' => $data['level'],
            'employee_status' => $data['employee_status'],
            'vnb_status' => 'active',
            'status' => 'Aktif',
            'manager_functional_id' => $functionalManager?->id ?? $manager->id,
            'manager_operational_id' => $operationalManager?->id,
        ];

        if ($employee) {
            $employee->fill($employeePayload)->save();
        } else {
            $employee = Employee::create($employeePayload);
        }

        $user->update(['employee_id' => $employee->id]);

        $employee->load('position');
        $careerStage = $employee->getCareerStage();
        if ($careerStage) {
            $employee->career_stage = $careerStage;
            $employee->save();
        }

        return $manager;
    }

    private function seedStaffEmployee(array $data, array $roles, ?Manager $functionalManager = null, ?Manager $operationalManager = null): Employee
    {
        $user = User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make('password'),
                'phone' => $data['whatsapp'] ?? null,
                'status' => 'active',
                'employee_id' => null,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles($roles);

        $employee = Employee::query()
            ->where('email', $data['email'])
            ->orWhere('employee_number', $data['employee_number'])
            ->first();

        $employeePayload = [
            'employee_number' => $data['employee_number'],
            'name' => $data['name'],
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'date_joined' => $data['date_joined'],
            'induction_date' => $data['induction_date'] ?? $data['date_joined'],
            'company' => $data['company'],
            'division_id' => $data['division_id'],
            'department_id' => $data['department_id'],
            'position_id' => $data['position_id'],
            'placement' => $data['placement'],
            'level' => $data['level'],
            'employee_status' => $data['employee_status'],
            'vnb_status' => 'active',
            'status' => 'Aktif',
            'manager_functional_id' => $functionalManager?->id,
            'manager_operational_id' => $operationalManager?->id,
        ];

        if ($employee) {
            $employee->fill($employeePayload)->save();
        } else {
            $employee = Employee::create($employeePayload);
        }

        $user->update(['employee_id' => $employee->id]);

        $employee->load('position');
        $careerStage = $employee->getCareerStage();
        if ($careerStage) {
            $employee->career_stage = $careerStage;
            $employee->save();
        }

        return $employee;
    }
}
