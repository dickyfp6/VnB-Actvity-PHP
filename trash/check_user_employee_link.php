<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Employee;

// Check user 'Zaki Zain'
$user = User::where('email', 'zaki@vnb.id')->first();
if (!$user) {
    $user = User::where('name', 'like', '%Zaki%')->first();
}

if ($user) {
    echo "=== User Info ===\n";
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Employee ID: {$user->employee_id}\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n\n";
    
    if ($user->employee_id) {
        $emp = Employee::find($user->employee_id);
        echo "=== Linked Employee ===\n";
        echo "Employee Name: {$emp->name}\n";
        echo "Career Stage: {$emp->getCareerStage()}\n";
        echo "Career Stage Code: {$emp->getCareerStageCode()}\n";
    } else {
        echo "⚠️ User tidak ter-link ke employee\n";
    }
} else {
    echo "User 'Zaki Zain' tidak ditemukan\n";
    echo "\nDaftar users:\n";
    User::select('id', 'email', 'name', 'employee_id')->get()->each(function($u) {
        echo "- {$u->email} ({$u->name}) => employee_id: " . ($u->employee_id ?? 'NULL') . "\n";
    });
}
