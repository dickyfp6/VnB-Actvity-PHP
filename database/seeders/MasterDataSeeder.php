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
            ['name' => 'Wismilak'],
        ];

        foreach ($companies as $company) {
            MasterCompany::create($company);
        }

        // 2. Master Division
        $divisions = [
            ['name' => 'Operations'],
            ['name' => 'Sales & Marketing'],
            ['name' => 'Finance'],
            ['name' => 'Human Resources'],
            ['name' => 'Quality Control'],
            ['name' => 'IT & Systems'],
            ['name' => 'Logistics'],
        ];

        foreach ($divisions as $division) {
            MasterDivision::create($division);
        }

        // 3. Master Department
        $departments = [
            ['name' => 'Production'],
            ['name' => 'Maintenance'],
            ['name' => 'Sales Team'],
            ['name' => 'Marketing'],
            ['name' => 'Accounting'],
            ['name' => 'Finance Planning'],
            ['name' => 'Recruitment'],
            ['name' => 'Training & Development'],
            ['name' => 'Quality Assurance'],
            ['name' => 'Lab Testing'],
            ['name' => 'Infrastructure'],
            ['name' => 'Applications'],
            ['name' => 'Warehouse'],
            ['name' => 'Distribution'],
        ];

        foreach ($departments as $department) {
            MasterDepartment::create($department);
        }

        // 4. Master Position
        $positions = [
            ['name' => 'Staff'],
            ['name' => 'Senior Staff'],
            ['name' => 'Supervisor'],
            ['name' => 'Manager'],
            ['name' => 'Senior Manager'],
            ['name' => 'Head of Division'],
            ['name' => 'Director'],
            ['name' => 'Intern'],
        ];

        foreach ($positions as $position) {
            MasterPosition::create($position);
        }

        // 5. Master Level
        $levels = [
            ['name' => 'Level 1 (Staff)'],
            ['name' => 'Level 2 (Senior Staff)'],
            ['name' => 'Level 3 (Supervisor)'],
            ['name' => 'Level 4 (Manager)'],
            ['name' => 'Level 5 (Senior Manager)'],
            ['name' => 'Level 6 (Head of Division)'],
            ['name' => 'Level 7 (Director)'],
        ];

        foreach ($levels as $level) {
            MasterLevel::create($level);
        }

        // 6. Master Employee Status
        $statuses = [
            ['name' => 'Training'],
            ['name' => 'Probation'],
            ['name' => 'Active'],
            ['name' => 'On Leave'],
            ['name' => 'Terminated'],
            ['name' => 'Resigned'],
        ];

        foreach ($statuses as $status) {
            MasterEmployeeStatus::create($status);
        }

        // 7. Master Placement
        $placements = [
            ['name' => 'HQ'],
            ['name' => 'Plant A'],
            ['name' => 'Plant B'],
            ['name' => 'Warehouse'],
            ['name' => 'Regional Office - Jakarta'],
            ['name' => 'Regional Office - Surabaya'],
            ['name' => 'Regional Office - Medan'],
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
