<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('employees')
    ->select('manager_functional_id', DB::raw('COUNT(*) as total'))
    ->groupBy('manager_functional_id')
    ->orderByDesc('total')
    ->get();

foreach ($rows as $r) {
    echo "Manager ID: {$r->manager_functional_id} => {$r->total}\n";
}
