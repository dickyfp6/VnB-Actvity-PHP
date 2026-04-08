<?php
// Check employees with manage_function career stage
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

// Count employees by career stage
echo "Employees by Career Stage:\n";

$employees = Employee::with('position')->get();

$stageCount = [];
foreach ($employees as $emp) {
    $stage = $emp->getCareerStage();
    if (!isset($stageCount[$stage])) {
        $stageCount[$stage] = [];
    }
    $stageCount[$stage][] = $emp;
}

foreach ($stageCount as $stage => $emps) {
    echo "  - $stage: " . count($emps) . " employees\n";
}

// Show manage_function employees
echo "\n\nEmployees with 'Manage Function' career stage:\n";

if (isset($stageCount['Manage Function'])) {
    foreach ($stageCount['Manage Function'] as $emp) {
        echo "- {$emp->name} ({$emp->position->name})\n";
        echo "  User ID: " . ($emp->user_id ? $emp->user_id : 'No user linked') . "\n";
        
        // Check framework items
        $careerStageCode = $emp->getCareerStageCode();
        $frameworkCount = DB::table('vnb_framework_items')
            ->where('career_stage', $careerStageCode)
            ->count();
        echo "  Framework Items: $frameworkCount\n\n";
    }
} else {
    echo "  No employees with 'Manage Function' career stage\n";
}

// Check random sample of framework items in manage_function
echo "\n\nSample manage_function framework items:\n";
$items = DB::table('vnb_framework_items')
    ->where('career_stage', 'manage_function')
    ->limit(5)
    ->get();

foreach ($items as $item) {
    echo "- {$item->behaviour} ({$item->phase}): {$item->integration_1}\n";
}
