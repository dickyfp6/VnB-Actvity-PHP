<?php
// Check VnB plans status
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\Employee;

echo "=== VnB Plans Status ===\n\n";

$totalPlans = VnbPlan::count();
$totalPlanItems = VnbPlanItem::count();

echo "Total Plans: $totalPlans\n";
echo "Total Plan Items: $totalPlanItems\n\n";

echo "Plans by Status:\n";
$byStatus = VnbPlan::selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

foreach ($byStatus as $status) {
    echo "  - {$status->status}: {$status->count}\n";
}

echo "\n\nEmployees with Career Stage but NO Plans:\n";

$employeesWithoutPlans = Employee::doesntHave('vnbPlans')->with('position')->get();
echo "Total: " . $employeesWithoutPlans->count() . "\n\n";

$stageCount = [];
foreach ($employeesWithoutPlans as $emp) {
    $stage = $emp->getCareerStage();
    if (!isset($stageCount[$stage])) {
        $stageCount[$stage] = 0;
    }
    $stageCount[$stage]++;
}

foreach ($stageCount as $stage => $count) {
    echo "  - $stage: $count employees without plans\n";
}

echo "\n\nSample employees without plans (first 5):\n";
$employeesWithoutPlans->take(5)->each(function($emp) {
    echo "- {$emp->name} ({$emp->position->name}) - Career Stage: " . $emp->getCareerStage() . "\n";
});

// Check if framework items are now available for all stages
echo "\n\n=== Framework Items Status ===\n";
$frameworkByStage = \Illuminate\Support\Facades\DB::table('vnb_framework_items')
    ->selectRaw('career_stage, COUNT(*) as count')
    ->groupBy('career_stage')
    ->get();

foreach ($frameworkByStage as $stage) {
    echo "  - {$stage->career_stage}: {$stage->count} items\n";
}
