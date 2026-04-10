<?php
// Test if user with manage_function career stage can now get plan
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\VnbFrameworkItem;

// Get Zaki Zain (the manage_function user)
$user = User::where('email', 'zaki@vnb.id')->first();

if (!$user) {
    echo "❌ User zaki@vnb.id not found\n";
    exit(1);
}

echo "User: {$user->name} (ID: {$user->id})\n";

// Get employee
$employee = Employee::where('user_id', $user->id)->first();

if (!$employee) {
    echo "❌ Employee for user not found\n";
    exit(1);
}

echo "Employee ID: {$employee->id}\n";
echo "Position: {$employee->position->name}\n";
echo "Career Stage: " . $employee->getCareerStage() . "\n";
echo "Career Stage Code: " . $employee->getCareerStageCode() . "\n\n";

// Check framework items for this career stage
$careerStageCode = $employee->getCareerStageCode();
$frameworkItems = VnbFrameworkItem::where('career_stage', $careerStageCode)->get();

echo "Framework Items for '{$careerStageCode}':\n";
echo "  - Total: " . $frameworkItems->count() . " items\n";

if ($frameworkItems->count() > 0) {
    echo "  ✓ Can create VnB plan! Framework found.\n";
    
    // Show sample items
    echo "\n  Sample items:\n";
    $frameworkItems->take(3)->each(function ($item, $i) {
        echo "    " . ($i+1) . ". {$item->behaviour} ({$item->phase}): {$item->integration_1}\n";
    });
} else {
    echo "  ❌ No framework items found - plan creation will fail!\n";
}
