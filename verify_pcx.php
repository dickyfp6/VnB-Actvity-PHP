<?php
// Check if we're running from Laravel
if (file_exists(__DIR__ . '/bootstrap/app.php')) {
    $app = require __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
} else {
    echo "Laravel app not found\n";
    exit(1);
}

$employees = DB::table('employees')
    ->select('employee_number', 'name', 'division', 'department', 'position', 'level')
    ->where('division', 'LIKE', '%Human Resource%')
    ->where('department', 'LIKE', '%Culture%')
    ->get();

echo "PCX Employees found: " . $employees->count() . "\n";
foreach ($employees as $emp) {
    echo "- {$emp->employee_number} | {$emp->name} | {$emp->position} | {$emp->level}\n";
}
echo "\n";

// Also check for employees eligible for intercomm (exact match)
$intercommEligible = DB::table('employees')
    ->select('employee_number', 'name', 'division', 'department', 'position', 'level')
    ->where(DB::raw('LOWER(TRIM(division))'), 'human resource')
    ->where(DB::raw('LOWER(TRIM(department))'), 'LIKE', '%people%culture%')
    ->get();

echo "Intercomm eligible employees (case-insensitive): " . $intercommEligible->count() . "\n";
foreach ($intercommEligible as $emp) {
    echo "- {$emp->employee_number} | {$emp->name} | {$emp->department}\n";
}
