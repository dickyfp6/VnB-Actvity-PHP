<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== DIAGNOSTIC: WHO HAS PLAN ITEMS & THEIR ROLES =====\n";

// 1. Employees with plan items and their user roles
echo "\n👥 Employees WITH plan items + their USER ROLES:\n";
$empWithPlans = DB::table('vnb_plan_items as vpi')
    ->join('employees as e', 'vpi.employee_id', '=', 'e.id')
    ->leftJoin('users as u', 'u.employee_id', '=', 'e.id')
    ->leftJoin('model_has_roles as mhr', 'u.id', '=', 'mhr.model_id')
    ->leftJoin('roles as r', 'mhr.role_id', '=', 'r.id')
    ->select('e.id', 'e.name', 'u.email', 'r.name as role', DB::raw('COUNT(vpi.id) as plan_items'))
    ->groupBy('e.id', 'e.name', 'u.email', 'r.name')
    ->orderBy('plan_items', 'DESC')
    ->get();

if ($empWithPlans->count() === 0) {
    echo "❌ NO employees have plan items!\n";
} else {
    foreach ($empWithPlans as $emp) {
        $role = $emp->role ?: 'NO_ROLE';
        echo "   ✓ {$emp->name} (Emp ID: {$emp->id}) - Role: {$role} → {$emp->plan_items} items\n";
    }
}

// 2. All employees in system
echo "\n\n📋 All EMPLOYEES in system (for reference):\n";
$allEmps = DB::table('employees as e')
    ->leftJoin('users as u', 'u.employee_id', '=', 'e.id')
    ->leftJoin('model_has_roles as mhr', 'u.id', '=', 'mhr.model_id')
    ->leftJoin('roles as r', 'mhr.role_id', '=', 'r.id')
    ->select('e.id', 'e.name', 'u.email', 'r.name as role')
    ->orderBy('e.id')
    ->get();

foreach ($allEmps as $emp) {
    $role = $emp->role ?: 'NO_ROLE';
    echo "   - {$emp->name} (ID: {$emp->id}) - Role: {$role}\n";
}

// 3. New Hire employees specifically
echo "\n\n🆕 NEW HIRE employees + plan items:\n";
$newHires = DB::table('employees as e')
    ->join('users as u', 'u.employee_id', '=', 'e.id')
    ->leftJoin('model_has_roles as mhr', 'u.id', '=', 'mhr.model_id')
    ->leftJoin('roles as r', 'mhr.role_id', '=', 'r.id')
    ->where('r.name', 'new_hire')
    ->select('e.id', 'e.name', 'u.email')
    ->get();

if ($newHires->count() === 0) {
    echo "❌ NO New Hire users found!\n";
} else {
    foreach ($newHires as $nh) {
        // Check if have plan items
        $items = DB::table('vnb_plan_items')->where('employee_id', $nh->id)->count();
        $status = $items > 0 ? "✓ HAVE {$items} items" : "❌ NO items";
        echo "   - {$nh->name} (Emp ID: {$nh->id}) {$status}\n";
    }
}

echo "\n===== END =====\n";
