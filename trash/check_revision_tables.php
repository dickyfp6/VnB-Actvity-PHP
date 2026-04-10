<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== VNB PLAN REVISIONS TABLE STRUCTURE =====\n";

$columns = DB::select("DESCRIBE vnb_plan_revisions");
echo "\n📋 vnb_plan_revisions columns:\n";
foreach ($columns as $col) {
    echo "   {$col->Field} ({$col->Type}) {$col->Null} - Key: {$col->Key}\n";
}

echo "\n\n===== VNB PLAN REVISION DETAILS TABLE STRUCTURE =====\n";

$columns = DB::select("DESCRIBE vnb_plan_revision_details");
echo "\n📋 vnb_plan_revision_details columns:\n";
foreach ($columns as $col) {
    echo "   {$col->Field} ({$col->Type}) {$col->Null} - Key: {$col->Key}\n";
}

echo "\n===== END =====\n";
