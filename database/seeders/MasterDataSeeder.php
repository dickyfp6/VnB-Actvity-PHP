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

        // 1. Master Company - Exactly 3 companies
        $companies = [
            ['name' => 'PT Wismilak Inti Makmur'],
            ['name' => 'PT Gelora Djaja'],
            ['name' => 'PT Gawih Djaja'],
            ['name' => 'PT Outsourcing (Khusus OS)'],
        ];

        foreach ($companies as $company) {
            MasterCompany::create($company);
        }

        // 2. Master Division - Wismilak organizational structure
        $divisions = [
            ['name' => 'General'],
            ['name' => 'Human Resource'],
            ['name' => 'Information Technology'],
            ['name' => 'Operations'],
            ['name' => 'Supply Chain Management'],
            ['name' => 'Commercial'],
            ['name' => 'Finance & Business Support'],
            ['name' => 'Strategic Research & Development'],
        ];

        foreach ($divisions as $division) {
            MasterDivision::create($division);
        }

        // Get division IDs for department assignment - using map to get all divisions in order
        $divisionList = MasterDivision::orderBy('id')->get();
        $divisionMap = [
            'GEN' => $divisionList[0]->id ?? 1,
            'HR' => $divisionList[1]->id ?? 2,
            'IT' => $divisionList[2]->id ?? 3,
            'OPS' => $divisionList[3]->id ?? 4,
            'SCM' => $divisionList[4]->id ?? 5,
            'COM' => $divisionList[5]->id ?? 6,
            'FBS' => $divisionList[6]->id ?? 7,
            'RD' => $divisionList[7]->id ?? 8,
        ];

        // 3. Master Department - Organized by division
        $departments = [
            // 0. General Division (GEN)
            ['division_id' => $divisionMap['GEN'], 'name' => 'General'],

            // 1. Human Resource Division (HR)
            ['division_id' => $divisionMap['HR'], 'name' => 'General'],
            ['division_id' => $divisionMap['HR'], 'name' => 'People & Culture Excellence (PCX)'],
            ['division_id' => $divisionMap['HR'], 'name' => 'Recruitment'],
            ['division_id' => $divisionMap['HR'], 'name' => 'C&B and HRIS'],

            // 2. Information Technology Division (IT)
            ['division_id' => $divisionMap['IT'], 'name' => 'General'],
            ['division_id' => $divisionMap['IT'], 'name' => 'Technical Support'],
            ['division_id' => $divisionMap['IT'], 'name' => 'Webapp Dev'],
            ['division_id' => $divisionMap['IT'], 'name' => 'SAP'],

            // 3. Operations Division (OPS)
            ['division_id' => $divisionMap['OPS'], 'name' => 'Primary Process'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Secondary Process'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Engineering & Maintenance'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Quality Assurance'],

            // 4. Supply Chain Management (SCM)
            ['division_id' => $divisionMap['SCM'], 'name' => 'PPIC'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Procurement'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Warehouse'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Logistics'],

            // 5. Commercial (COM)
            ['division_id' => $divisionMap['COM'], 'name' => 'Brand Management'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Sales Retail'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Area Sales'],

            // 5. Finance & Business Support (FBS)
            ['division_id' => $divisionMap['FBS'], 'name' => 'Financial Planning & Analysis (FP&A)'],
            ['division_id' => $divisionMap['FBS'], 'name' => 'Accounting & Tax'],
            ['division_id' => $divisionMap['FBS'], 'name' => 'Treasury & Cash Management'],
            ['division_id' => $divisionMap['FBS'], 'name' => 'Internal Audit & Risk Management'],
            ['division_id' => $divisionMap['FBS'], 'name' => 'IT Infrastructure'],
            ['division_id' => $divisionMap['FBS'], 'name' => 'IT Digital Transformation / Software Dev'],
            ['division_id' => $divisionMap['FBS'], 'name' => 'General Affairs & Asset Management'],
            ['division_id' => $divisionMap['FBS'], 'name' => 'Legal & Compliance'],

            // 6. Strategic Research & Development (R&D)
            ['division_id' => $divisionMap['RD'], 'name' => 'Product Development'],
            ['division_id' => $divisionMap['RD'], 'name' => 'Packaging Development'],
            ['division_id' => $divisionMap['RD'], 'name' => 'Regulatory Affairs'],
        ];

        foreach ($departments as $department) {
            MasterDepartment::create($department);
        }

        // 4. Master Position / Golongan
        $positions = [
            ['name' => 'Harian'],
            ['name' => 'Mingguan'],
            ['name' => 'Borongan'],
            ['name' => 'Staf/Supervisor'],
            ['name' => 'Manager'],
        ];

        foreach ($positions as $position) {
            MasterPosition::create($position);
        }

        // 5. Master Level - Employee Hierarchy Levels
        $levels = [
            ['name' => 'Non-Staff'],
            ['name' => 'Staff'],
            ['name' => 'Supervisor'],
            ['name' => 'Manager'],
            ['name' => 'Kepala Tim'],
            ['name' => 'General Manager'],
            ['name' => 'Kepala Divisi'],
            ['name' => 'Direktur'],
        ];

        foreach ($levels as $level) {
            MasterLevel::create($level);
        }

        // 6. Master Employee Status - Employment Contract Types
        $statuses = [
            ['name' => 'OS'],
            ['name' => 'PKWT'],
            ['name' => 'PKWTT'],
        ];

        foreach ($statuses as $status) {
            MasterEmployeeStatus::create($status);
        }

        // 7. Master Placement - Exactly 24 locations
        $placements = [
            ['name' => 'Bandung'],
            ['name' => 'Banjarmasin'],
            ['name' => 'Bengkulu'],
            ['name' => 'Bogor'],
            ['name' => 'Buntaran'],
            ['name' => 'Cirebon'],
            ['name' => 'Jakarta'],
            ['name' => 'Jember'],
            ['name' => 'Jombang'],
            ['name' => 'Kediri'],
            ['name' => 'Malang'],
            ['name' => 'Medan'],
            ['name' => 'Padangsidimpuan'],
            ['name' => 'Pamekasan'],
            ['name' => 'Pati'],
            ['name' => 'Pematangsiantar'],
            ['name' => 'Purwokerto'],
            ['name' => 'Semarang'],
            ['name' => 'Solo'],
            ['name' => 'Jambi'],
            ['name' => 'Surabaya'],
            ['name' => 'Tangerang'],
            ['name' => 'Tegal'],
            ['name' => 'Yogyakarta'],
        ];

        foreach ($placements as $placement) {
            MasterPlacement::create($placement);
        }

        echo "Master Data seeder completed:\n";
        echo "- Companies: " . MasterCompany::count() . "\n";
        echo "- Divisions: " . MasterDivision::count() . " (Operations, SCM, Commercial, HC&CA, Finance, R&D)\n";
        echo "- Departments: " . MasterDepartment::count() . " (organized by division)\n";
        echo "- Positions: " . MasterPosition::count() . "\n";
        echo "- Levels: " . MasterLevel::count() . "\n";
        echo "- Employee Statuses: " . MasterEmployeeStatus::count() . "\n";
        echo "- Placements: " . MasterPlacement::count() . "\n";
    }
}
