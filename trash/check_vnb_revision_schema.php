<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking vnb_plan_revisions table schema ===\n\n";

if (!Schema::hasTable('vnb_plan_revisions')) {
    echo "❌ Table 'vnb_plan_revisions' does not exist!\n";
} else {
    echo "✅ Table 'vnb_plan_revisions' exists\n\n";
    
    $columns = Schema::getColumns('vnb_plan_revisions');
    echo "Columns in vnb_plan_revisions:\n";
    foreach ($columns as $column) {
        echo "  - {$column['name']} ({$column['type']})\n";
    }
    
    echo "\n";
    
    // Check for both column names
    if (Schema::hasColumn('vnb_plan_revisions', 'vnb_plan_id')) {
        echo "✅ Column 'vnb_plan_id' exists\n";
    } else {
        echo "❌ Column 'vnb_plan_id' does NOT exist\n";
    }
    
    if (Schema::hasColumn('vnb_plan_revisions', 'plan_id')) {
        echo "✅ Column 'plan_id' exists\n";
    } else {
        echo "❌ Column 'plan_id' does NOT exist\n";
    }
    
    // Check for other needed columns
    echo "\nOther important columns:\n";
    foreach (['version_number', 'submitted_by', 'snapshot', 'status'] as $col) {
        if (Schema::hasColumn('vnb_plan_revisions', $col)) {
            echo "  ✅ $col\n";
        } else {
            echo "  ❌ $col\n";
        }
    }
    
    // Try to fetch a sample to check foreign key constraint
    echo "\n\nSample data (if exists):\n";
    $count = DB::table('vnb_plan_revisions')->count();
    echo "Total rows: $count\n";
    
    if ($count > 0) {
        $sample = DB::table('vnb_plan_revisions')->limit(1)->first();
        echo "First row:\n";
        foreach ((array) $sample as $key => $value) {
            echo "  $key: " . (is_string($value) && strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
        }
    }
}

echo "\n=== Checking vnb_plans table ===\n";
$columns = Schema::getColumns('vnb_plans');
if (in_array('submitted_at', array_map(fn($c) => $c['name'], $columns))) {
    echo "✅ vnb_plans has 'submitted_at' column\n";
} else {
    echo "❌ vnb_plans missing 'submitted_at' column\n";
}
