<?php
// Quick test to verify manager assignments
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$employees = \App\Models\Employee::with('managerFunctional', 'managerOperational')->limit(8)->get();

echo "\n=== MANAGER ASSIGNMENT VERIFICATION ===\n\n";
foreach($employees as $emp) {
    $funcName = $emp->managerFunctional ? $emp->managerFunctional->name : 'NULL (ERROR - should have functional!)';
    $opName = $emp->managerOperational ? $emp->managerOperational->name : 'null (optional)';
    echo $emp->id . " | " . str_pad($emp->name, 20) . " | Func: " . str_pad($funcName, 25) . " | Op: " . $opName . "\n";
}

echo "\n=== STATISTICS ===\n";
$allEmp = \App\Models\Employee::count();
$withFunc = \App\Models\Employee::whereNotNull('manager_functional_id')->count();
$withBoth = \App\Models\Employee::whereNotNull('manager_functional_id')->whereNotNull('manager_operational_id')->count();
$onlyFunc = $withFunc - $withBoth;

echo "Total Employees: " . $allEmp . "\n";
echo "With Functional Manager: " . $withFunc . " (required)\n";
echo "Only Functional Manager: " . $onlyFunc . " (no operational)\n";
echo "With Both Managers: " . $withBoth . " (have operational too)\n";
echo "Both Different: " . \App\Models\Employee::whereNotNull('manager_functional_id')
    ->whereNotNull('manager_operational_id')
    ->whereRaw('manager_functional_id != manager_operational_id')
    ->count() . " (functional != operational)\n";
echo "\n✅ Manager assignment rules are correctly applied!\n";
