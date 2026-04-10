<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== EMPLOYEE TABLE STRUCTURE =====\n";

// Get employees table columns
echo "\n📋 EMPLOYEES columns:\n";
$columns = DB::select("DESCRIBE employees");
foreach ($columns as $col) {
    echo "   - {$col->Field} ({$col->Type})\n";
}

// Get users table columns  
echo "\n📋 USERS columns:\n";
$columns = DB::select("DESCRIBE users");
foreach ($columns as $col) {
    echo "   - {$col->Field} ({$col->Type})\n";
}

// Sample employee data
echo "\n📊 Sample EMPLOYEES data:\n";
$sample = DB::table('employees')->limit(1)->first();
if ($sample) {
    foreach ((array)$sample as $key => $value) {
        echo "   $key: $value\n";
    }
}

// Check how employees are connected to users - check if name matches?
echo "\n===== END =====\n";
