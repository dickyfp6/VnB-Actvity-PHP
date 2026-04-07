<?php

namespace Database\Seeders;

use App\Models\MasterDivision;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use App\Models\MasterLevel;
use App\Models\MasterEmployeeStatus;
use App\Models\MasterPlacement;
use App\Models\MasterCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        MasterCompany::truncate();
        MasterDivision::truncate();
        MasterDepartment::truncate();
        MasterPosition::truncate();
        MasterLevel::truncate();
        MasterEmployeeStatus::truncate();
        MasterPlacement::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Master Company
        $companies = [
            ['name' => 'Wismilak', 'code' => 'WSK', 'description' => 'PT Wismilak Interbis'],
        ];

        foreach ($companies as $company) {
            MasterCompany::create($company);
        }

        // 2. Master Division
        $divisions = [
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Operasional Produksi'],
            ['name' => 'Sales & Marketing', 'code' => 'SALES', 'description' => 'Penjualan & Pemasaran'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Keuangan & Akuntansi'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Sumber Daya Manusia'],
            ['name' => 'Quality Control', 'code' => 'QC', 'description' => 'Pengendalian Kualitas'],
            ['name' => 'IT & Systems', 'code' => 'IT', 'description' => 'Teknologi Informasi'],
            ['name' => 'Logistics', 'code' => 'LOG', 'description' => 'Logistik & Distribusi'],
        ];

        foreach ($divisions as $division) {
            MasterDivision::create($division);
        }

        // 3. Master Department
        $departments = [
            // Operations
            ['name' => 'Production', 'code' => 'PROD', 'division_id' => 1],
            ['name' => 'Maintenance', 'code' => 'MAINT', 'division_id' => 1],
            
            // Sales & Marketing
            ['name' => 'Sales Team', 'code' => 'SALES_T', 'division_id' => 2],
            ['name' => 'Marketing', 'code' => 'MKT', 'division_id' => 2],
            
            // Finance
            ['name' => 'Accounting', 'code' => 'ACC', 'division_id' => 3],
            ['name' => 'Finance Planning', 'code' => 'FP', 'division_id' => 3],
            
            // HR
            ['name' => 'Recruitment', 'code' => 'REC', 'division_id' => 4],
            ['name' => 'Training & Development', 'code' => 'T&D', 'division_id' => 4],
            
            // QC
            ['name' => 'Quality Assurance', 'code' => 'QA', 'division_id' => 5],
            ['name' => 'Lab Testing', 'code' => 'LAB', 'division_id' => 5],
            
            // IT
            ['name' => 'Infrastructure', 'code' => 'INFRA', 'division_id' => 6],
            ['name' => 'Applications', 'code' => 'APP', 'division_id' => 6],
            
            // Logistics
            ['name' => 'Warehouse', 'code' => 'WHS', 'division_id' => 7],
            ['name' => 'Distribution', 'code' => 'DIST', 'division_id' => 7],
        ];

        foreach ($departments as $department) {
            MasterDepartment::create($department);
        }

        // 4. Master Position
        $positions = [
            ['name' => 'Staff', 'code' => 'STF', 'description' => 'Entry Level'],
            ['name' => 'Senior Staff', 'code' => 'SR_STF', 'description' => 'Intermediate Level'],
            ['name' => 'Supervisor', 'code' => 'SVR', 'description' => 'Team Lead'],
            ['name' => 'Manager', 'code' => 'MGR', 'description' => 'Department Manager'],
            ['name' => 'Senior Manager', 'code' => 'SR_MGR', 'description' => 'Senior Manager'],
            ['name' => 'Head of Division', 'code' => 'HOD', 'description' => 'Division Head'],
            ['name' => 'Director', 'code' => 'DIR', 'description' => 'Executive Director'],
            ['name' => 'Intern', 'code' => 'INTERN', 'description' => 'Magang'],
        ];

        foreach ($positions as $position) {
            MasterPosition::create($position);
        }

        // 5. Master Level
        $levels = [
            ['name' => 'Level 1 (Staff)', 'code' => 'L1', 'description' => 'Entry Level Staff'],
            ['name' => 'Level 2 (Senior Staff)', 'code' => 'L2', 'description' => 'Senior Staff'],
            ['name' => 'Level 3 (Supervisor)', 'code' => 'L3', 'description' => 'Supervisor Level'],
            ['name' => 'Level 4 (Manager)', 'code' => 'L4', 'description' => 'Manager Level'],
            ['name' => 'Level 5 (Senior Manager)', 'code' => 'L5', 'description' => 'Senior Manager Level'],
            ['name' => 'Level 6 (Head of Division)', 'code' => 'L6', 'description' => 'Head of Division'],
            ['name' => 'Level 7 (Director)', 'code' => 'L7', 'description' => 'Executive Director'],
        ];

        foreach ($levels as $level) {
            MasterLevel::create($level);
        }

        // 6. Master Employee Status
        $statuses = [
            ['name' => 'Training', 'code' => 'TRAIN', 'description' => 'Sedang dalam masa training'],
            ['name' => 'Probation', 'code' => 'PROB', 'description' => 'Masa percobaan'],
            ['name' => 'Active', 'code' => 'ACT', 'description' => 'Aktif bekerja'],
            ['name' => 'On Leave', 'code' => 'LEAVE', 'description' => 'Sedang cuti'],
            ['name' => 'Terminated', 'code' => 'TERM', 'description' => 'Sudah diterminasi'],
            ['name' => 'Resigned', 'code' => 'RES', 'description' => 'Sudah mengundurkan diri'],
        ];

        foreach ($statuses as $status) {
            MasterEmployeeStatus::create($status);
        }

        // 7. Master Placement
        $placements = [
            ['name' => 'HQ', 'code' => 'HQ', 'description' => 'Kantor Pusat'],
            ['name' => 'Plant A', 'code' => 'PLANT_A', 'description' => 'Pabrik A'],
            ['name' => 'Plant B', 'code' => 'PLANT_B', 'description' => 'Pabrik B'],
            ['name' => 'Warehouse', 'code' => 'WHS', 'description' => 'Gudang Pusat'],
            ['name' => 'Regional Office - Jakarta', 'code' => 'RO_JKT', 'description' => 'Kantor Regional Jakarta'],
            ['name' => 'Regional Office - Surabaya', 'code' => 'RO_SBY', 'description' => 'Kantor Regional Surabaya'],
            ['name' => 'Regional Office - Medan', 'code' => 'RO_MDN', 'description' => 'Kantor Regional Medan'],
        ];

        foreach ($placements as $placement) {
            MasterPlacement::create($placement);
        }

        echo "Master Data seeder completed:\n";
        echo "- Companies: " . MasterCompany::count() . "\n";
        echo "- Divisions: " . MasterDivision::count() . "\n";
        echo "- Departments: " . MasterDepartment::count() . "\n";
        echo "- Positions: " . MasterPosition::count() . "\n";
        echo "- Levels: " . MasterLevel::count() . "\n";
        echo "- Employee Statuses: " . MasterEmployeeStatus::count() . "\n";
        echo "- Placements: " . MasterPlacement::count() . "\n";
    }
}
