<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== DATABASE SEED ANALYSIS =====\n\n";

// List all tables
$tables = DB::select("SHOW TABLES");

foreach ($tables as $tableObj) {
    $tableName = (array)$tableObj;
    $tableName = array_values($tableName)[0];
    $count = DB::table($tableName)->count();
    
    // Skip system/cache tables
    if (in_array($tableName, ['cache', 'cache_locks', 'activity_logs', 'migrations'])) {
        echo "⏭️  $tableName: $count rows (system table)\n";
        continue;
    }
    
    echo "📊 $tableName: $count rows\n";
}

echo "\n===== CRITICAL TABLES CHECK =====\n";

// Check important tables
$criticalTables = [
    'roles' => 'User roles (admin, manager, new_hire, etc)',
    'permissions' => 'System permissions',
    'role_has_permissions' => 'Role-permission mappings',
    'model_has_roles' => 'User-role assignments',
    'master_companies' => 'Company data',
    'master_divisions' => 'Division data',
    'master_departments' => 'Department data',
    'master_positions' => 'Job position data',
    'master_levels' => 'Employee level data',
    'master_placements' => 'Work placement locations',
    'master_employee_statuses' => 'Employment status types',
    'users' => 'System users',
    'employees' => 'Employee records',
    'managers' => 'Manager records',
    'vnb_framework_items' => 'VnB framework definitions',
    'vnb_periods' => 'VnB period assignments',
    'vnb_plans' => 'User VnB plans',
    'vnb_plan_items' => 'Plan activity items',
];

$missing = [];
$empty = [];
$populated = [];

foreach ($criticalTables as $table => $description) {
    if (!DB::connection()->getSchemaBuilder()->hasTable($table)) {
        $missing[] = "❌ $table - $description [TABLE NOT FOUND]";
        continue;
    }
    
    $count = DB::table($table)->count();
    if ($count === 0) {
        $empty[] = "⚠️  $table - $description [0 rows]";
    } else {
        $populated[] = "✅ $table - $description [$count rows]";
    }
}

echo "\n✅ POPULATED TABLES:\n";
foreach ($populated as $msg) echo "   $msg\n";

if (!empty($empty)) {
    echo "\n⚠️  EMPTY TABLES (May need data):\n";
    foreach ($empty as $msg) echo "   $msg\n";
}

if (!empty($missing)) {
    echo "\n❌ MISSING TABLES:\n";
    foreach ($missing as $msg) echo "   $msg\n";
}

echo "\n===== END =====\n";
