<?php

namespace Database\Seeders;

use App\Models\VnbPeriod;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VnbPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        VnbPeriod::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get all employees (New Hires)
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $inductionDate = $employee->induction_date ?? now();

            // Create 3 phases for each employee
            // Phase 1: 0-2 months
            // Phase 2: 2-4 months  
            // Phase 3: 4-6 months

            // Phase 1 (1-3 bulan)
            VnbPeriod::create([
                'employee_id' => $employee->id,
                'phase_number' => 1,
                'start_date' => $inductionDate,
                'end_date' => $inductionDate->copy()->addMonths(2),
                'cutoff_date' => $inductionDate->copy()->addMonths(2)->day(25),
                'status' => 'in_progress',
            ]);

            // Phase 2 (4-6 bulan)
            VnbPeriod::create([
                'employee_id' => $employee->id,
                'phase_number' => 2,
                'start_date' => $inductionDate->copy()->addMonths(2)->addDay(),
                'end_date' => $inductionDate->copy()->addMonths(4),
                'cutoff_date' => $inductionDate->copy()->addMonths(4)->day(25),
                'status' => 'not_started',
            ]);

            // Phase 3 (>6 bulan)
            VnbPeriod::create([
                'employee_id' => $employee->id,
                'phase_number' => 3,
                'start_date' => $inductionDate->copy()->addMonths(4)->addDay(),
                'end_date' => $inductionDate->copy()->addMonths(6),
                'cutoff_date' => $inductionDate->copy()->addMonths(6)->day(25),
                'status' => 'not_started',
            ]);
        }

        echo "VnB Period seeder completed: " . VnbPeriod::count() . " periods created (" . $employees->count() . " employees × 3 phases)\n";
    }
}
