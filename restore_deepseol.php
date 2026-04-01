<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

// Get Deepseol employee
$deepseol = Employee::find(2);

if (!$deepseol) {
    echo "Employee Deepseol not found!\n";
    exit(1);
}

echo "=== RESTORE DEEPSEOL PASSWORD ===\n";
echo "Employee: " . $deepseol->name . "\n";
echo "Employee Number: " . $deepseol->employee_number . "\n";
echo "Email: " . $deepseol->email . "\n";
echo "Current User ID: " . ($deepseol->user_id ?? 'NULL') . "\n";

// Old password for Deepseol
$oldPassword = 'deepseol890';

// Check if Deepseol has a user account
$user = $deepseol->user;

if (!$user) {
    // User doesn't exist for this employee
    // We need to check if there's a conflicting user with same email
    $conflictUser = User::where('email', $deepseol->email)->first();
    
    if ($conflictUser) {
        echo "\nWARNING: Email conflict detected!\n";
        echo "User ID: " . $conflictUser->id . "\n";
        echo "User Name: " . $conflictUser->name . "\n";
        echo "User Roles: " . implode(', ', $conflictUser->getRoleNames()->toArray()) . "\n";
        echo "\nThis user was taking over Deepseol's email (probably during manager reset).\n";
        echo "We cannot create a new user for Deepseol without changing either email.\n\n";
        
        // Check if this is manager dicky
        $manager = $conflictUser->manager;
        if ($manager) {
            echo "Linked Manager: " . $manager->name . "\n";
            echo "\nRECOMMENDATION:\n";
            echo "1. Change Manager Dicky's email to something else\n";
            echo "2. Then we can create user account for Deepseol with original email\n";
        }
    } else {
        echo "\nNo user account found for Deepseol.\n";
        echo "Creating new user account with password: deepseol890\n\n";
        
        $user = User::create([
            'name' => $deepseol->name,
            'email' => $deepseol->email,
            'password' => Hash::make($oldPassword),
            'status' => 'active',
            'email_verified_at' => now(),
            'employee_id' => $deepseol->id,
        ]);
        
        $user->assignRole('new_hire');
        
        // Link user to employee
        $deepseol->update(['user_id' => $user->id]);
        
        echo "SUCCESS: User account created for Deepseol!\n";
        echo "User ID: " . $user->id . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Password: " . $oldPassword . "\n";
        echo "Role: new_hire\n";
    }
} else {
    // User exists, just restore password
    echo "\nUser account found for Deepseol.\n";
    echo "User ID: " . $user->id . "\n";
    echo "Updating password to: deepseol890\n\n";
    
    $user->update([
        'password' => Hash::make($oldPassword),
    ]);
    
    echo "SUCCESS: Password restored!\n";
    echo "Email: " . $user->email . "\n";
    echo "New Password: " . $oldPassword . "\n";
}
