<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$names = [
    'Fajar Nugroho',
    'Agung Prasetyo',
    'Nanda Saputra',
    'Maya Dwi Lestari',
    'Intan Maharani',
];

$users = User::whereIn('name', $names)->with(['roles', 'manager'])->get();

echo "=== Current Roles ===\n";
foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    $isManager = !is_null($user->manager);
    echo "- {$user->name}: {$roles} | Has manager record: " . ($isManager ? 'YES' : 'NO') . "\n";
}
