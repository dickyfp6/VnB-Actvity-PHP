<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$users = DB::table('users')->get();
echo "Users in Database:\n";
echo str_repeat('=', 80) . "\n";
foreach($users as $u) {
    echo sprintf("%-25s | %-30s | NIP: %s\n", $u->name, $u->email, $u->employee_number);
}
echo str_repeat('=', 80) . "\n";
