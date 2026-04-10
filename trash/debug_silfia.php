<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get plan for Silfia Mei (employee ID 4)
$silfiaPlan = App\Models\VnbPlan::where('employee_id', 4)->with('items.frameworkItem')->first();

if (!$silfiaPlan) {
    echo "No plan found for Silfia Mei (employee ID 4)\n";
    exit;
}

echo "Plan for Silfia Mei:\n";
echo "Title: {$silfiaPlan->title}\n";
echo "Employee Level: {$silfiaPlan->employee->level}\n";
echo "Total Items: {$silfiaPlan->items->count()}\n\n";

// Check framework items
$frameworkItems = App\Models\VnbFrameworkItem::where('career_stage', 'manage_self_non_staff')->get(['id', 'behaviour', 'phase', 'career_stage']);
echo "Framework items for manage_self_non_staff: " . $frameworkItems->count() . "\n";

echo "\nFirst 5 plan items:\n";
foreach ($silfiaPlan->items->take(5) as $item) {
    echo "Activity: {$item->activity_title}, Framework ID: {$item->framework_item_id}, Description: " . substr($item->description, 0, 50) . "...\n";
}

echo "\nFirst 5 framework items (manage_self_non_staff):\n";
foreach ($frameworkItems->take(5) as $fw) {
    echo "Behaviour: {$fw->behaviour}, Phase: {$fw->phase}, Career Stage: {$fw->career_stage}\n";
}
