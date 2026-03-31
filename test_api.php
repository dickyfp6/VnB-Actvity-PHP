<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\VnbFrameworkItem;
use App\Models\VnbPlan;

// Check New Hire user
$user = User::whereHas('roles', function($q) { $q->where('name', 'new_hire'); })->first();
if ($user) {
    echo "✓ New Hire User: {$user->email} (ID: {$user->id})\n";
    echo "  Employee ID: {$user->employee_id}\n";
    
    if ($user->employee_id) {
        $emp = Employee::find($user->employee_id);
        if ($emp) {
            echo "  Employee: {$emp->name}\n";
            echo "  Level: {$emp->level}\n";
            echo "  Induction: {$emp->induction_date}\n";
            
            // Check framework items for this level
            $level = $emp->level ?? 'manage_self_non_staff';
            $count = VnbFrameworkItem::where('career_stage', $level)->count();
            echo "  Framework items for {$level}: {$count}\n";
            
            // Check existing plan
            $existingPlan = VnbPlan::where('employee_id', $emp->id)->first();
            if ($existingPlan) {
                echo "  Existing plan: {$existingPlan->title} (Status: {$existingPlan->status})\n";
            } else {
                echo "  No existing plan\n";
            }
        } else {
            echo "  Employee not found!\n";
        }
    } else {
        echo "  NOT LINKED TO EMPLOYEE!\n";
    }
} else {
    echo "✗ No new_hire user found\n";
}
