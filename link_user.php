<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Employee;

// Find first employee
$emp = Employee::first();
if (!$emp) {
    die("No employee found!");
}

// Find new_hire user
$user = User::where('email', 'newhire@vnb.local')->first();
if (!$user) {
    die("User not found!");
}

// Link user to employee
$user->update(['employee_id' => $emp->id]);

echo "✓ User {$user->email} linked to Employee {$emp->name} (ID: {$emp->id})\n";
echo "  Employee Level: {$emp->level}\n";
echo "  Induction Date: {$emp->induction_date}\n";
