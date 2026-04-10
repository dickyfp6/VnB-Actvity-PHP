<?php
// List sample employee users with their career stages
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\VnbFrameworkItem;

// Get all employee users
$users = User::where('role', 'employee')->get();
echo "Users with employee role: " . $users->count() . "\n\n";

// Check for manage_function users specifically
echo "Looking for users with 'Manage Function' career stage...\n\n";

$manage_function_users = [];
foreach ($users as $user) {
    $employee = Employee::where('user_id', $user->id)->first();
    if ($employee) {
        $careerStage = $employee->getCareerStage();
        if ($careerStage === 'Manage Function') {
            $manage_function_users[] = [
                'user' => $user,
                'employee' => $employee
            ];
        }
    }
}

if (empty($manage_function_users)) {
    echo "No employee users with 'Manage Function' career stage found.\n\n";
    echo "Sample of available employee users:\n";
    
    $users->take(5)->each(function($user, $i) {
        $employee = Employee::where('user_id', $user->id)->first();
        if ($employee) {
            echo ($i+1) . ". {$user->name} ({$user->email}): " . $employee->getCareerStage() . "\n";
        }
    });
} else {
    echo "✓ Found " . count($manage_function_users) . " user(s) with 'Manage Function' career stage!\n\n";
    
    foreach ($manage_function_users as $item) {
        $user = $item['user'];
        $employee = $item['employee'];
        
        echo "User: {$user->name}\n";
        echo "  Email: {$user->email}\n";
        echo "  Career Stage: " . $employee->getCareerStage() . "\n";
        echo "  Career Stage Code: " . $employee->getCareerStageCode() . "\n";
        
        // Check if framework is available
        $frameworkCount = VnbFrameworkItem::where('career_stage', $employee->getCareerStageCode())->count();
        echo "  Framework Items Available: $frameworkCount\n";
        echo "  Status: " . ($frameworkCount > 0 ? "✓ Ready to create plans" : "❌ No framework") . "\n\n";
    }
}
