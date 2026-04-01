<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\Manager;

// List all users with managers and employees
echo "=== ALL USERS ===\n";
$users = User::with(['manager', 'employee'])->get();

foreach ($users as $user) {
    echo "\nUser ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    
    if ($user->manager) {
        echo "Linked Manager: " . $user->manager->name . " (ID: " . $user->manager->id . ")\n";
    }
    
    if ($user->employee) {
        echo "Linked Employee: " . $user->employee->name . " (ID: " . $user->employee->id . ")\n";
    }
    
    echo "Employee ID: " . ($user->employee_id ?? 'NULL') . "\n";
}

// Specifically check user 6
echo "\n\n=== DEBUG USER ID 6 ===\n";
$user6 = User::find(6);
if ($user6) {
    echo "User ID: 6\n";
    echo "Name: " . $user6->name . "\n";
    echo "Email: " . $user6->email . "\n";
    echo "Roles: " . implode(', ', $user6->getRoleNames()->toArray()) . "\n";
    echo "Employee ID: " . ($user6->employee_id ?? 'NULL') . "\n";
    
    $manager6 = Manager::where('user_id', 6)->first();
    if ($manager6) {
        echo "Linked Manager: " . $manager6->name . " (ID: " . $manager6->id . ")\n";
    }
}
