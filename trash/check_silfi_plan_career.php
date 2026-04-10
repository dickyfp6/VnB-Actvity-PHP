<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== CHECK SILFI'S VNB_PLANS =====\n";

$silfiPlan = DB::table('vnb_plans as vp')
    ->join('employees as e', 'vp.employee_id', '=', 'e.id')
    ->where('e.name', 'LIKE', '%Silfi%')
    ->select('vp.*', 'e.name')
    ->first();

if (!$silfiPlan) {
    echo "❌ Silfi's plan NOT found!\n";
} else {
    echo "✓ Found Silfi's VNB Plan:\n\n";
    foreach ((array)$silfiPlan as $key => $val) {
        if ($key === 'name') continue;
        $display = $val ?: '(NULL)';
        echo "   $key: $display\n";
    }
}

// Check what career_stage her plan items have
echo "\n\n📊 Silfi's plan items career_stage:\n";
$itemsCareer = DB::table('vnb_plan_items as vpi')
    ->join('employees as e', 'vpi.employee_id', '=', 'e.id')
    ->join('vnb_framework_items as vfi', 'vpi.framework_item_id', '=', 'vfi.id')
    ->where('e.name', 'LIKE', '%Silfi%')
    ->select('vfi.career_stage', DB::raw('COUNT(*) as count'))
    ->groupBy('vfi.career_stage')
    ->get();

foreach ($itemsCareer as $row) {
    echo "   {$row->career_stage}: {$row->count} items\n";
}

echo "\n===== END =====\n";
