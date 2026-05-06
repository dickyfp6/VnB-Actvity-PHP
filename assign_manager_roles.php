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

$users = User::whereIn('name', $names)->get();

echo "Assigning manager role to:\n";
foreach ($users as $user) {
    if (!$user->hasRole('manager')) {
        $user->assignRole('manager');
        echo "✓ {$user->name}\n";
    } else {
        echo "- {$user->name} (already has manager role)\n";
    }
}

echo "\nDone!\n";
