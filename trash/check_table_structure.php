<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== TABLE STRUCTURE ANALYSIS =====\n";

// Get vnb_plan_items columns
echo "\n📋 vnb_plan_items COLUMNS:\n";
$columns = DB::select("DESCRIBE vnb_plan_items");
foreach ($columns as $col) {
    echo "   - {$col->Field} ({$col->Type}) {$col->Null}\n";
}

// Get vnb_framework_items columns
echo "\nvnb_framework_items COLUMNS:\n";
$columns = DB::select("DESCRIBE vnb_framework_items");
foreach ($columns as $col) {
    echo "   - {$col->Field} ({$col->Type}) {$col->Null}\n";
}

// Check sample plan items
echo "\n📊 SAMPLE vnb_plan_items DATA (first 5):\n";
$samples = DB::table('vnb_plan_items')->limit(5)->get();
foreach ($samples as $item) {
    echo "   ID: {$item->id}, User: {$item->user_id}, Career: {$item->career_stage}, Framework: {$item->framework_item_id}\n";
}

// Check sample framework items  
echo "\n📌 SAMPLE vnb_framework_items DATA (first 5):\n";
$samples = DB::table('vnb_framework_items')->limit(5)->get();
foreach ($samples as $item) {
    echo json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

// Count by career stage
echo "\n👥 vnb_plan_items by CAREER STAGE:\n";
$counts = DB::table('vnb_plan_items')
    ->select('career_stage', DB::raw('COUNT(*) as count'))
    ->groupBy('career_stage')
    ->get();

foreach ($counts as $row) {
    echo "   {$row->career_stage}: {$row->count} items\n";
}

echo "\n===== END =====\n";
