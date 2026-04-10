<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== VNB_PLANS TABLE STRUCTURE & DATA =====\n";

// Table structure
echo "\n📋 vnb_plans columns:\n";
$columns = DB::select("DESCRIBE vnb_plans");
foreach ($columns as $col) {
    echo "   - {$col->Field} ({$col->Type})\n";
}

// Sample data
echo "\n📊 vnb_plans data:\n";
$plans = DB::table('vnb_plans')->get();
echo "Total: {$plans->count()}\n";
foreach ($plans as $plan) {
    echo "\n   Plan ID: {$plan->id}\n";
    foreach ((array)$plan as $key => $val) {
        $display = $val ?: '(NULL)';
        echo "      $key: $display\n";
    }
}

echo "\n===== END =====\n";
