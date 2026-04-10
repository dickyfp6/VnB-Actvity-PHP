<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$employees = DB::table('employees')
    ->select('id', 'name', 'career_stage')
    ->limit(10)
    ->get();

echo "=== Career Stage Check ===\n\n";
foreach ($employees as $emp) {
    echo "ID {$emp->id}: {$emp->name} => {$emp->career_stage}\n";
}
