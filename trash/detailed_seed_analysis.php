<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== DETAILED ANALYSIS OF SQL FILE VS DATABASE =====\n";

// Read the SQL file to see what was intended to be seeded
$sqlFile = file_get_contents("database/vnb_wismilak (hampir).sql");

// Find all master data and important tables
$importantTables = [
    'master_companies' => 'Company Masters',
    'master_divisions' => 'Division Masters',
    'master_departments' => 'Department Masters',
    'master_positions' => 'Position Masters',
    'master_levels' => 'Level Masters',
    'master_placements' => 'Placement Masters',
    'master_employee_statuses' => 'Employee Status Masters',
    'vnb_evidence_types' => 'VnB Evidence Types',
    'vnb_framework_items' => 'VnB Framework Items',
    'imports' => 'Import Records',
    'notifications' => 'Notification System',
    'vnb_progress' => 'VnB Progress Tracking',
];

echo "\n📋 CHECKING IMPORTANT MASTER DATA TABLES:\n";
foreach ($importantTables as $table => $desc) {
    if (!DB::connection()->getSchemaBuilder()->hasTable($table)) {
        echo "\n   ❓ $table - $desc\n";
        echo "      Table tidak ada di database!\n";
        continue;
    }
    
    $count = DB::table($table)->count();
    
    // Check if table has seed data in SQL file
    $hasSeedInSql = strpos($sqlFile, "INSERT INTO `$table`") !== false;
    
    if ($count === 0 && !$hasSeedInSql) {
        echo "\n   ❌ $table - $desc\n";
        echo "      [$count rows] - NO seed data in SQL file\n";
    } elseif ($count === 0 && $hasSeedInSql) {
        echo "\n   ⚠️  $table - $desc\n";
        echo "      [$count rows] - Has seed in SQL file but NOT imported to DB\n";
    } else {
        echo "\n   ✅ $table - $desc\n";
        echo "      [$count rows] - Properly seeded\n";
    }
}

echo "\n\n===== LOOKING FOR IMPORTANT MISSING TABLES =====\n";

// Check if vnb-related lookup tables exist
$allTables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
$tableNames = array_map(fn($t) => $t->TABLE_NAME, $allTables);

$expectedTables = [
    'vnb_evidence_types',
    'vnb_cancellation_reasons', 
    'vnb_approval_types',
];

foreach ($expectedTables as $table) {
    if (!in_array($table, $tableNames)) {
        echo "\n   ❓ $table - TIDAK DITEMUKAN\n";
        echo "      (Table ini mungkin diperlukan untuk system)\n";
    } else {
        $count = DB::table($table)->count();
        echo "\n   ✅ $table - Exists [$count rows]\n";
    }
}

echo "\n===== SEED DATA RECOMMENDATIONS =====\n";

$recommendations = [];

// Check for missing/empty critical data
if (DB::table('roles')->count() === 0) {
    $recommendations[] = "🔴 CRITICAL: Roles tidak di-seed! Users tidak bisa login.";
}

if (DB::table('permissions')->count() === 0) {
    $recommendations[] = "🔴 CRITICAL: Permissions tidak di-seed! Authorization akan error.";
}

if (DB::table('master_positions')->count() === 0) {
    $recommendations[] = "🟡 HIGH: Master Positions kosong - tidak ada data posisi.";
}

if (DB::table('master_departments')->count() === 0) {
    $recommendations[] = "🟡 HIGH: Master Departments kosong - tidak ada data departemen.";
}

if (DB::table('vnb_framework_items')->count() === 0) {
    $recommendations[] = "🔴 CRITICAL: VnB Framework Items kosong - VnB system tidak bisa berfungsi.";
}

if (DB::table('master_levels')->count() === 0) {
    $recommendations[] = "🟡 HIGH: Master Levels kosong - Career stage mapping tidak bisa ditentukan.";
}

if (DB::table('employees')->count() === 0) {
    $recommendations[] = "🔴 CRITICAL: Employees kosong - tidak ada user data untuk test.";
}

if ($recommendations) {
    echo "\n";
    foreach ($recommendations as $rec) {
        echo "$rec\n";
    }
} else {
    echo "\n✅ All critical seed data is present!\n";
}

echo "\n===== END =====\n";
