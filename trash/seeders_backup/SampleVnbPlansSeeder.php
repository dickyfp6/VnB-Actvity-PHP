<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbPeriod;
use App\Models\VnbFrameworkItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SampleVnbPlansSeeder extends Seeder
{
    /**
     * Auto-generate VnB plans for ALL employees with populated deliverables
     * Ensures all dummy data has plans ready immediately after seeding
     */
    public function run(): void
    {
        $this->command->info('🔄 Creating VnB plans for all employees...');

        // Get ALL employees
        $employees = Employee::all();
        $processedCount = 0;
        $itemsCreatedCount = 0;

        foreach ($employees as $employee) {
            $careerStageCode = $employee->getCareerStageCode();
            
            // Get framework items for this career stage
            $frameworkItems = VnbFrameworkItem::where('career_stage', $careerStageCode)
                ->get()
                ->groupBy('phase');

            if ($frameworkItems->isEmpty()) {
                continue;
            }

            // Get or create period
            $period = VnbPeriod::where('employee_id', $employee->id)->first();
            if (!$period) {
                $period = VnbPeriod::create([
                    'employee_id' => $employee->id,
                    'phase_number' => 1,
                    'start_date' => $employee->induction_date ?? now(),
                    'end_date' => ($employee->induction_date ?? now())->addMonths(6),
                    'cutoff_date' => ($employee->induction_date ?? now())->addMonths(6)->day(25),
                    'status' => 'in_progress',
                ]);
            }

            // Create plan
            $plan = VnbPlan::create([
                'employee_id' => $employee->id,
                'period_id' => $period->id,
                'phase_number' => $period->phase_number,
                'title' => 'Rencana VnB - ' . $employee->name,
                'description' => 'Auto-generated dari framework ' . $careerStageCode,
                'planning_mode' => 'adjust_all',
                'status' => 'draft',
            ]);

            // Create items from framework
            $itemsToInsert = [];
            foreach ($frameworkItems as $phaseNumber => $items) {
                foreach ($items as $item) {
                    $integrationParts = [];
                    if ($item->integration_1) {
                        $integrationParts[] = $item->integration_1;
                    }
                    if ($item->integration_2) {
                        $integrationParts[] = $item->integration_2;
                    }
                    $description = !empty($integrationParts) 
                        ? implode(' | ', $integrationParts) 
                        : 'Activity for ' . $item->behaviour;

                    $itemsToInsert[] = [
                        'plan_id' => $plan->id,
                        'framework_item_id' => $item->id,
                        'activity_title' => $item->behaviour . ' - Phase ' . $phaseNumber,
                        'description' => $description,
                        'integration_1' => $item->integration_1,
                        'integration_2' => $item->integration_2,
                        'due_date' => now()->addDays(7)->format('Y-m-d'),
                        'activity_date' => now()->addDays(7)->format('Y-m-d'),
                        'deliverables' => '-',  // Will be populated by PopulateDeliverablesFromFramework command
                        'behavior_metrics' => json_encode([$item->behaviour, 'phase_' . $phaseNumber]),
                        'submission_status' => 'draft',
                        'status' => 'draft',
                        'completion_percentage' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($itemsToInsert)) {
                VnbPlanItem::insert($itemsToInsert);
                $itemsCreatedCount += count($itemsToInsert);
            }

            $processedCount++;
            
            // Show progress every 20 employees
            if ($processedCount % 20 === 0) {
                $this->command->info("✓ Processed {$processedCount} employees...");
            }
        }

        $totalPlans = VnbPlan::count();
        $totalItems = VnbPlanItem::count();
        $this->command->info("✅ All employee plans created!");
    }
}
