<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== VNB PLANS SEED STATUS (CORRECTED) =====\n";

// 1. Total vnb_plan_items
$totalItems = DB::table('vnb_plan_items')->count();
echo "✓ Total vnb_plan_items: $totalItems\n";

// 2. Distribution by phase (needs JOIN to vnb_framework_items)
echo "\n📊 Distribution by PHASE (via framework):\n";
$phaseDistribution = DB::table('vnb_plan_items as vpi')
    ->join('vnb_framework_items as vfi', 'vpi.framework_item_id', '=', 'vfi.id')
    ->select('vfi.phase', DB::raw('COUNT(*) as count'))
    ->groupBy('vfi.phase')
    ->get();

if ($phaseDistribution->count() === 0) {
    echo "❌ NO DATA - vnb_plan_items is EMPTY or not linked!\n";
} else {
    foreach ($phaseDistribution as $row) {
        echo "   Phase '{$row->phase}': {$row->count} items\n";
    }
}

// 3. Career stage distribution from vnb_framework_items
echo "\n👥 Distribution by CAREER STAGE:\n";
$careerDistribution = DB::table('vnb_plan_items as vpi')
    ->join('vnb_framework_items as vfi', 'vpi.framework_item_id', '=', 'vfi.id')
    ->select('vfi.career_stage', DB::raw('COUNT(*) as count'))
    ->groupBy('vfi.career_stage')
    ->get();

foreach ($careerDistribution as $row) {
    echo "   {$row->career_stage}: {$row->count} items\n";
}

// 4. Check vnb_framework_items phases
echo "\n📌 VnB Framework Items by PHASE:\n";
$frameworkPhases = DB::table('vnb_framework_items')
    ->select('phase', DB::raw('COUNT(*) as count'))
    ->groupBy('phase')
    ->get();

foreach ($frameworkPhases as $row) {
    echo "   Phase '{$row->phase}': {$row->count} framework items\n";
}

// 5. Check if plan items have framework_item_id linked
echo "\n🔗 Framework Link Status:\n";
$withLink = DB::table('vnb_plan_items')->whereNotNull('framework_item_id')->count();
$withoutLink = DB::table('vnb_plan_items')->whereNull('framework_item_id')->count();
echo "   With framework_item_id: $withLink\n";
echo "   Without framework_item_id (NULL): $withoutLink\n";

// 6. Check Silfi's plan items
echo "\n🔍 Silfi Mei's Plan Items:\n";
$silfiPlan = DB::table('vnb_plan_items as vpi')
    ->join('employees as e', 'vpi.employee_id', '=', 'e.id')
    ->join('vnb_framework_items as vfi', 'vpi.framework_item_id', '=', 'vfi.id')
    ->where('e.name', 'LIKE', '%Silfi%')
    ->select('e.name', 'vfi.career_stage', 'vfi.phase', 'vfi.behaviour', 'vpi.activity_title')
    ->orderBy('vfi.career_stage')
    ->orderBy('vfi.phase')
    ->orderBy('vfi.behaviour')
    ->get();

if ($silfiPlan->count() === 0) {
    echo "❌ Silfi Mei has NO plan items!\n";
} else {
    echo "✓ Found {$silfiPlan->count()} plan items for Silfi\n";
    $grouped = $silfiPlan->groupBy('career_stage');
    foreach ($grouped as $stage => $items) {
        echo "\n   Career Stage: $stage\n";
        $byPhase = $items->groupBy('phase');
        foreach ($byPhase as $phase => $phaseItems) {
            echo "      Phase: $phase ({$phaseItems->count()} items)\n";
        }
    }
}

echo "\n===== END =====\n";
