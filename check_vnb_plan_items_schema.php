<?php
// Check VnbPlanItems table schema
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "VnbPlanItems table columns:\n";
$columns = Schema::getColumns('vnb_plan_items');
foreach ($columns as $col) {
    echo "  - {$col['name']} ({$col['type']})\n";
}

echo "\n\nDESCRIBE vnb_plan_items:\n";
$tableInfo = DB::select("DESCRIBE vnb_plan_items");
foreach ($tableInfo as $field) {
    echo "  - {$field->Field} ({$field->Type}) - Null: {$field->Null}, Key: {$field->Key}\n";
}
