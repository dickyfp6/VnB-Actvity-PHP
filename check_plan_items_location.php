<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== WHERE ARE THE 84 PLAN ITEMS? =====\n";

// Sample plan items to see what employee_id they have
echo "\n📊 Sample 10 plan items:\n";
$samples = DB::table('vnb_plan_items')
    ->select('id', 'employee_id', 'plan_id', 'framework_item_id', 'activity_title')
    ->limit(10)
    ->get();

foreach ($samples as $item) {
    echo "   ID: {$item->id} - employee_id: {$item->employee_id}, plan_id: {$item->plan_id}\n";
}

// Check vnb_plans table
echo "\n\n📋 vnb_plans in database:\n";
$plans = DB::table('vnb_plans')->get();
if ($plans->count() === 0) {
    echo "❌ NO vnb_plans!\n";
} else {
    foreach ($plans as $plan) {
        echo "   ID: {$plan->id} - employee_id: {$plan->employee_id}, career_stage: {$plan->career_stage}\n";
    }
}

// Unique employee_id in vnb_plan_items
echo "\n\n🔍 Which employee_ids have plan items:\n";
$empIds = DB::table('vnb_plan_items')
    ->select('employee_id', DB::raw('COUNT(*) as count'))
    ->groupBy('employee_id')
    ->get();

if ($empIds->count() === 0) {
    echo "❌ ALL plan items have NULL employee_id!\n";
} else {
    foreach ($empIds as $row) {
        echo "   employee_id: {$row->employee_id} ({$row->count} items)\n";
    }
}

echo "\n===== END =====\n";
