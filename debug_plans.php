<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get all plans
$plans = App\Models\VnbPlan::with('employee')->get();
echo "All VnB Plans:\n";
foreach ($plans as $plan) {
    echo "Employee: {$plan->employee->name} (ID: {$plan->employee->id}, Level: {$plan->employee->level}), Plan: {$plan->title}, Items: " . $plan->items->count() . "\n";
}

echo "\n=== Employee Levels ===\n";
$employees = App\Models\Employee::get(['id', 'name', 'level']);
foreach ($employees as $emp) {
    echo "ID: {$emp->id}, Name: {$emp->name}, Level: {$emp->level}\n";
}
