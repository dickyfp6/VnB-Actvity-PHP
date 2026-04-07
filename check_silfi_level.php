<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== SILFI'S EMPLOYEE DATA =====\n";

$silfi = DB::table('employees as e')
    ->join('users as u', 'u.employee_id', '=', 'e.id')
    ->join('model_has_roles as mhr', 'u.id', '=', 'mhr.model_id')
    ->join('roles as r', 'mhr.role_id', '=', 'r.id')
    ->where('e.name', 'LIKE', '%Silfi%')
    ->select('e.*', 'u.name as user_name', 'r.name as user_role')
    ->first();

if ($silfi) {
    echo "✓ Employee: {$silfi->name}\n";
    echo "   User Role: {$silfi->user_role}\n";
    echo "   Level: {$silfi->level}\n";
    echo "   Employee Status: {$silfi->employee_status}\n";
}

echo "\n===== END =====\n";
