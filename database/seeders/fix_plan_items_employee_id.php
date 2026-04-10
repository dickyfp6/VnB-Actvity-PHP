<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== FIXING PLAN ITEMS EMPLOYEE_ID =====\n";

// 1. Check before
echo "\n📊 BEFORE fix:\n";
$before = DB::table('vnb_plan_items')
    ->whereNull('employee_id')
    ->count();
echo "   Plan items with NULL employee_id: {$before}\n";

// 2. Run the fix
echo "\n🔧 Running UPDATE query...\n";
$updated = DB::statement(
    'UPDATE vnb_plan_items vpi
     INNER JOIN vnb_plans vp ON vpi.plan_id = vp.id
     SET vpi.employee_id = vp.employee_id
     WHERE vpi.employee_id IS NULL'
);

// 3. Check after
echo "\n📊 AFTER fix:\n";
$after = DB::table('vnb_plan_items')
    ->whereNull('employee_id')
    ->count();
echo "   Plan items with NULL employee_id: {$after}\n";
echo "   ✅ Updated!\n";

// 4. Show distribution per employee
echo "\n👥 Plan items now per employee:\n";
$distribution = DB::table('vnb_plan_items as vpi')
    ->join('employees as e', 'vpi.employee_id', '=', 'e.id')
    ->select('e.name', 'e.id', DB::raw('COUNT(*) as count'))
    ->groupBy('e.id', 'e.name')
    ->orderBy('e.id')
    ->get();

foreach ($distribution as $emp) {
    echo "   {$emp->name} (ID: {$emp->id}): {$emp->count} items\n";
}

// 5. Check Silfi specifically
echo "\n🔍 Silfi Mei's plan items now:\n";
$silfiItems = DB::table('vnb_plan_items as vpi')
    ->join('employees as e', 'vpi.employee_id', '=', 'e.id')
    ->join('vnb_framework_items as vfi', 'vpi.framework_item_id', '=', 'vfi.id')
    ->where('e.name', 'LIKE', '%Silfi%')
    ->select('vfi.career_stage', 'vfi.phase', DB::raw('COUNT(*) as count'))
    ->groupBy('vfi.career_stage', 'vfi.phase')
    ->get();

if ($silfiItems->count() === 0) {
    echo "   ❌ Still NO items\n";
} else {
    foreach ($silfiItems as $item) {
        echo "   ✓ {$item->career_stage} - Phase {$item->phase}: {$item->count} items\n";
    }
}

echo "\n===== FIX COMPLETE =====\n";
