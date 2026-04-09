<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Manager;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Fixing Manager User Account Role Issue ===\n\n";

// Get manager with ID 2
$manager = Manager::with('user')->find(2);

if (!$manager) {
    echo "❌ Manager ID 2 not found!\n";
    exit(1);
}

$user = $manager->user;
if (!$user) {
    echo "❌ Manager has no associated user account!\n";
    exit(1);
}

echo "Manager: {$manager->name} (ID: {$manager->id})\n";
echo "User ID: {$user->id}\n";
echo "Current Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n\n";

try {
    // Remove 'new_hire' role if present
    if ($user->hasRole('new_hire')) {
        echo "Removing 'new_hire' role...\n";
        $user->removeRole('new_hire');
    }
    
    // Ensure 'manager' role is assigned
    if (!$user->hasRole('manager')) {
        echo "Assigning 'manager' role...\n";
        $user->assignRole('manager');
    }
    
    $user = $user->fresh();
    echo "\n✅ Success! User roles have been fixed.\n";
    echo "New Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n\n";
    
    // Test if the reset credential would work now
    echo "Attempting to verify the reset credential flow would work...\n";
    if ($user && ($user->employee_id || $user->hasRole('new_hire'))) {
        echo "❌ Still blocked!\n";
    } else {
        echo "✅ Reset credential validation should now pass!\n";
    }
    
} catch (\Throwable $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
