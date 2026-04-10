<?php
// Simulate creating plan for an employee (like getOrCreateEmployeePlan)
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;
use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;
use App\Models\VnbPeriod;

echo "=== Testing Plan Creation Flow ===\n\n";

// Get first employee without plan
$employee = Employee::doesntHave('vnbPlans')->first();

if (!$employee) {
    echo "❌ No employee without plan found\n";
    exit(1);
}

echo "Employee: {$employee->name} ({$employee->position->name})\n";
echo "Career Stage: " . $employee->getCareerStage() . "\n";
echo "Career Stage Code: " . $employee->getCareerStageCode() . "\n\n";

$careerStageCode = $employee->getCareerStageCode();

// Get framework items
$frameworkItems = VnbFrameworkItem::where('career_stage', $careerStageCode)
    ->get()
    ->groupBy('phase');

echo "Framework Items Available: " . $frameworkItems->count() . " phases\n";
$totalItems = $frameworkItems->sum(fn($phase) => $phase->count());
echo "  Total behaviors across phases: $totalItems\n\n";

if ($frameworkItems->isEmpty()) {
    echo "❌ No framework items found!\n";
    exit(1);
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
    echo "Created period: Period ID {$period->id}\n\n";
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

echo "Created Plan: ID {$plan->id}\n";
echo "  Title: {$plan->title}\n";
echo "  Status: {$plan->status}\n\n";

// Insert plan items
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

echo "Inserting " . count($itemsToInsert) . " plan items...\n";

try {
    VnbPlanItem::insert($itemsToInsert);
    echo "✓ Successfully inserted!\n\n";
    
    // Verify
    $plan->refresh();
    $itemCount = $plan->items()->count();
    echo "Plan now has: $itemCount items\n";
    
    // Show sample
    echo "\nSample items:\n";
    $plan->items()->take(3)->get()->each(function($item, $i) {
        echo ($i+1) . ". {$item->activity_title}\n";
        echo "   Due: {$item->due_date}\n";
        echo "   Status: {$item->submission_status}\n";
    });
    
} catch (\Exception $e) {
    echo "❌ Error inserting items:\n";
    echo $e->getMessage() . "\n\n";
    echo "SQL Error: " . ($e->getPrevious() ? $e->getPrevious()->getMessage() : 'N/A') . "\n";
}
