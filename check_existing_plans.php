<?php
// Check the 2 existing plans
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\VnbPlan;

echo "=== Existing Plans ===\n\n";

$plans = VnbPlan::with('employee', 'items')->get();

foreach ($plans as $plan) {
    echo "Plan ID: {$plan->id}\n";
    echo "  Employee: {$plan->employee->name} (ID: {$plan->employee->id})\n";
    echo "  Career Stage: " . $plan->employee->getCareerStage() . "\n";
    echo "  Career Stage Code: " . $plan->employee->getCareerStageCode() . "\n";
    echo "  Status: {$plan->status}\n";
    echo "  Items Count: " . $plan->items->count() . "\n";
    echo "  Created: {$plan->created_at}\n\n";
}

// Try to call the endpoint function directly to see if it works now
echo "=== Testing getOrCreateNewHirePlan for sample employee ===\n\n";

$employee = \App\Models\Employee::doesntHave('vnbPlans')->first();
if ($employee) {
    echo "Testing with: {$employee->name} ({$employee->position->name})\n";
    echo "Career Stage: " . $employee->getCareerStage() . "\n";
    echo "Career Stage Code: " . $employee->getCareerStageCode() . "\n\n";
    
    // Check framework items for this employee
    $careerStageCode = $employee->getCareerStageCode();
    $frameworkItems = \App\Models\VnbFrameworkItem::where('career_stage', $careerStageCode)->get();
    
    echo "Framework Items Available: " . $frameworkItems->count() . "\n";
    
    if ($frameworkItems->count() > 0) {
        echo "Sample Items:\n";
        $frameworkItems->groupBy('behaviour')->take(3)->each(function($items, $behaviour) {
            echo "  - $behaviour: " . $items->count() . " variations\n";
        });
    }
}
