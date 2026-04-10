<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Manager;
use App\Models\User;
use App\Models\Employee;

echo "=== Diagnosing Manager User Account Issues ===\n\n";

// Get manager with ID 2
$manager = Manager::with('user')->find(2);

if (!$manager) {
    echo "❌ Manager ID 2 not found!\n";
    exit(1);
}

echo "Manager ID: {$manager->id}\n";
echo "Manager Name: {$manager->name}\n";
echo "Manager Email: {$manager->email}\n";
echo "Manager User ID: {$manager->user_id}\n\n";

if (!$manager->user) {
    echo "❌ Manager has no associated user account!\n";
    exit(1);
}

$user = $manager->user;
echo "User ID: {$user->id}\n";
echo "User Name: {$user->name}\n";
echo "User Email: {$user->email}\n";
echo "User Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
echo "User Employee ID: {$user->employee_id}\n";
echo "User Status: {$user->status}\n\n";

// Check for issues
$issues = [];

if ($user->employee_id) {
    $issues[] = "✗ User has employee_id=$user->employee_id (should be null for manager)";
    $employee = Employee::find($user->employee_id);
    if ($employee) {
        echo "  Linked Employee: {$employee->name} (ID: {$employee->id})\n";
    }
}

if ($user->hasRole('employee')) {
    $issues[] = "✗ User has 'employee' role assigned";
}

if (empty($issues)) {
    echo "✅ No issues found! User account is properly configured.\n";
} else {
    echo "❌ Issues found:\n";
    foreach ($issues as $issue) {
        echo "  $issue\n";
    }
}
