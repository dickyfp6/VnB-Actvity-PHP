<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get plan with all items and framework
$silfiaPlan = App\Models\VnbPlan::where('employee_id', 4)->with('items.frameworkItem')->first();

echo "=== Silfia Plan Analysis ===\n";
echo "Employee: {$silfiaPlan->employee->name}\n";
echo "Level: {$silfiaPlan->employee->level}\n";
echo "Total Items: " . $silfiaPlan->items->count() . "\n\n";

// Check which career stages are present in framework items
$careerStages = $silfiaPlan->items->map(function($item) {
    return $item->frameworkItem->career_stage;
})->unique();

echo "Career Stages in Plan Items:\n";
foreach ($careerStages as $stage) {
    echo "  - $stage\n";
}

echo "\nFirst item details:\n";
$firstItem = $silfiaPlan->items->first();
echo "Activity: {$firstItem->activity_title}\n";
echo "Description: {$firstItem->description}\n";
echo "Framework Career Stage: {$firstItem->frameworkItem->career_stage}\n";
