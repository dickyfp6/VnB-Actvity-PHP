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
        ];

        foreach ($companies as $company) {
            MasterCompany::create($company);
        }

        // 2. Master Division - Wismilak organizational structure
        $divisions = [
            ['name' => 'Operations (Pusat Produksi)'],
            ['name' => 'Supply Chain Management (SCM)'],
            ['name' => 'Commercial (Sales & Marketing)'],
            ['name' => 'Human Capital & Corporate Affairs'],
            ['name' => 'Finance & Business Support'],
            ['name' => 'Strategic Research & Development (R&D)'],
        ];

        foreach ($divisions as $division) {
            MasterDivision::create($division);
        }

        // Get division IDs for department assignment - using map to get all divisions in order
        $divisionList = MasterDivision::orderBy('id')->get();
        $divisionMap = [
            'OPS' => $divisionList[0]->id ?? 1,      // Operations
            'SCM' => $divisionList[1]->id ?? 2,      // SCM
            'COM' => $divisionList[2]->id ?? 3,      // Commercial
            'HCCA' => $divisionList[3]->id ?? 4,     // Human Capital & Corporate Affairs
            'FBS' => $divisionList[4]->id ?? 5,      // Finance & Business Support
            'RD' => $divisionList[5]->id ?? 6,       // R&D
        ];

        // 3. Master Department - Organized by division
        $departments = [
            // 1. Operations Division (OPS)
            ['division_id' => $divisionMap['OPS'], 'name' => 'Primary Process (Pemrosesan bahan baku/daun)'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Secondary Process (Linting/Produksi barang jadi)'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Engineering & Maintenance'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Quality Assurance (QA)'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Quality Control (QC)'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Health, Safety, & Environment (HSE)'],
            ['division_id' => $divisionMap['OPS'], 'name' => 'Continuous Improvement (Lean Manufacturing)'],
            
            // 2. Supply Chain Management (SCM)
            ['division_id' => $divisionMap['SCM'], 'name' => 'Production Planning & Inventory Control (PPIC)'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Procurement (Direct Material)'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Procurement (Indirect Material/Services)'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Warehouse Raw Material'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Warehouse Finished Goods'],
            ['division_id' => $divisionMap['SCM'], 'name' => 'Logistics & Fleet Management'],
            
            // 3. Commercial (Sales & Marketing)
            ['division_id' => $divisionMap['COM'], 'name' => 'Brand Management (Product A)'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Brand Management (Product B)'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Trade Marketing / Field Marketing'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Sales Retail / General Trade'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Key Account Management / Modern Trade'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Marketing Strategy & Research'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Area Sales (East Region)'],
            ['division_id' => $divisionMap['COM'], 'name' => 'Area Sales (West Region)'],
            
            // 4. Human Capital & Corporate Affairs (HCCA)
            ['division_id' => $divisionMap['HCCA'], 'name' => 'People & Culture Excellence (PCX)'],
            ['division_id' => $divisionMap['HCCA'], 'name' => 'Internal Communication (Intercomm)'],
            ['division_id' => $divisionMap['HCCA'], 'name' => 'Talent Acquisition & Employer Branding'],
            ['division_id' => $divisionMap['HCCA'], 'name' => 'Learning & Organizational Development'],
            ['division_id' => $divisionMap['HCCA'], 'name' => 'Compensation & Benefit (Payroll)'],
            ['division_id' => $divisionMap['HCCA'], 'name' => 'Industrial Relation (IR)'],
            ['division_id' => $divisionMap['HCCA'], 'name' => 'Corporate Social Responsibility (CSR)'],
            
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
