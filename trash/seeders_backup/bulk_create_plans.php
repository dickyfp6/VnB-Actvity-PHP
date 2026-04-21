<?php
// Bulk create plans for all employees without plans
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;
use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;
use App\Models\VnbPeriod;

$employeesWithoutPlans = Employee::doesntHave('vnbPlans')->get();

echo "=== Bulk Creating Plans for " . $employeesWithoutPlans->count() . " Employees ===\n\n";

$planCreated = 0;
$itemsCreated = 0;
$errors = [];

foreach ($employeesWithoutPlans as $employee) {
    try {
        $careerStageCode = $employee->getCareerStageCode();
        
        // Get framework items
        $frameworkItems = VnbFrameworkItem::where('career_stage', $careerStageCode)
            ->get()
            ->groupBy('phase');
        
        if ($frameworkItems->isEmpty()) {
            $errors[] = "{$employee->name}: No framework items for $careerStageCode";
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
        
        // Prepare items
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
                $description = !empty($integrationParts) ? implode(' | ', $integrationParts) : 'Activity for ' . $item->behaviour;

                $itemsToInsert[] = [
                    'plan_id' => $plan->id,
                    'framework_item_id' => $item->id,
                    'activity_title' => $item->behaviour . ' - Phase ' . $phaseNumber,
                    'description' => $description,
                    'integration_1' => $item->integration_1,
                    'integration_2' => $item->integration_2,
                    'due_date' => now()->addDays(7)->format('Y-m-d'),
                    'activity_date' => now()->addDays(7)->format('Y-m-d'),
                    'deliverables' => '-',
                    'behavior_metrics' => json_encode([$item->behaviour, 'phase_' . $phaseNumber]),
                    'submission_status' => 'draft',
                    'status' => 'draft',
                    'completion_percentage' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insert items
        if (!empty($itemsToInsert)) {
            VnbPlanItem::insert($itemsToInsert);
            $itemsCreated += count($itemsToInsert);
        }
        
        $planCreated++;
        
    } catch (\Exception $e) {
        $errors[] = "{$employee->name}: " . $e->getMessage();
    }
}

echo "✅ Completed:\n";
echo "  - Plans created: $planCreated\n";
echo "  - Items created: $itemsCreated\n\n";

if (!empty($errors)) {
    echo "⚠️  Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\n";
}

// Verify
echo "=== Final Status ===\n";
$totalEmployees = Employee::count();
$employeesWithPlans = Employee::has('vnbPlans')->count();
$totalPlans = VnbPlan::count();
$totalPlanItems = VnbPlanItem::count();

echo "Total Employees: $totalEmployees\n";
echo "Employees with Plans: $employeesWithPlans\n";
echo "Total Plans: $totalPlans\n";
echo "Total Plan Items: $totalPlanItems\n\n";

// By career stage
echo "Plans by Career Stage:\n";
$byStage = VnbPlan::selectRaw('career_stage, COUNT(*) as count')
    ->join('employees', 'vnb_plans.employee_id', '=', 'employees.id')
    ->groupBy('career_stage')
    ->get();

foreach ($byStage as $stage) {
    echo "  - {$stage->career_stage}: {$stage->count} plans\n";
}
