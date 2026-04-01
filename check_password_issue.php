<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\Manager;

// Find user with NIP or username 5026221036
$username = '5026221036';

$users = User::where('email', 'like', "%$username%")
    ->orWhere('name', 'like', '%Deepseol%')
    ->get();

if ($users->count() > 0) {
    echo "=== Found " . $users->count() . " users ===\n";
    foreach ($users as $user) {
        echo "\nUser ID: " . $user->id . "\n";
        echo "Name: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
        echo "Employee ID: " . ($user->employee_id ?? 'NULL') . "\n";
    }
} else {
    echo "No user found with username or email: $username or name containing 'Deepseol'\n";
    echo "Let's try to find employees with that NIP...\n";
    
    $employee = Employee::where('employee_number', $username)->first();
    if ($employee) {
        echo "\n=== Found Employee ===\n";
        echo "ID: " . $employee->id . "\n";
        echo "Name: " . $employee->name . "\n";
        echo "Email: " . $employee->email . "\n";
        echo "Employee Number: " . $employee->employee_number . "\n";
        echo "User ID: " . ($employee->user_id ?? 'NULL - but might have user via email') . "\n";
        
        // Check if there's a user linked to this employee
        if ($employee->user_id) {
            $linkedUser = User::find($employee->user_id);
            if ($linkedUser) {
                echo "\nLinked User Found:\n";
                echo "User ID: " . $linkedUser->id . "\n";
                echo "User Email: " . $linkedUser->email . "\n";
                echo "User Roles: " . implode(', ', $linkedUser->getRoleNames()->toArray()) . "\n";
            }
        }
        
        // Check if there's a user with same email
        $userWithSameEmail = User::where('email', $employee->email)->first();
        if ($userWithSameEmail) {
            echo "\nUser with same email found:\n";
            echo "User ID: " . $userWithSameEmail->id . "\n";
            echo "User Email: " . $userWithSameEmail->email . "\n";
            echo "User Roles: " . implode(', ', $userWithSameEmail->getRoleNames()->toArray()) . "\n";
        }
    } else {
        echo "No employee found with NIP: $username\n";
        
        // List all users and employees to debug
        echo "\n=== All Users (limit 10) ===\n";
        $allUsers = User::limit(10)->get();
        foreach ($allUsers as $user) {
            echo "- " . $user->email . " (" . implode(', ', $user->getRoleNames()->toArray()) . ")\n";
        }
    }
}
