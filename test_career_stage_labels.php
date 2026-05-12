<?php
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

echo "=== Testing Career Stage Labels from Database ===\n\n";

// Check framework configs
echo "Framework Stage Configs:\n";
$configs = DB::table('vnb_framework_stage_configs')
    ->select('id', 'career_stage', 'label')
    ->get();
foreach ($configs as $config) {
    echo "  {$config->career_stage} => {$config->label}\n";
}

echo "\n--- Testing 5 Employees ---\n";
$employees = Employee::limit(5)->get();

foreach ($employees as $emp) {
    echo "\nEmployee: {$emp->name} (ID: {$emp->id}, Level: {$emp->level})\n";
    echo "  getCareerStage() [should be label from DB]: " . ($emp->getCareerStage() ?? 'NULL') . "\n";
    echo "  getCareerStageCode() [should be code]: " . ($emp->getCareerStageCode() ?? 'NULL') . "\n";
}

echo "\n=== Test Complete ===\n";
?>
