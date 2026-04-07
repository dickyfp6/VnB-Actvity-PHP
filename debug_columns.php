<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = \DB::select('SHOW FULL COLUMNS FROM vnb_plan_items');
foreach ($columns as $col){
    if($col->Field == 'status' || $col->Field == 'submission_status'){
        echo "Field: {$col->Field} | Type: {$col->Type}\n"; 
    }
}
