#!/usr/bin/env php
<?php
/**
 * Direct database check and fix - run with: php check_fix_users.php
 */

require 'bootstrap/app.php';
$app = require_app('bootstrap/app.php');
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "USER AND EMAIL DIAGNOSTICS\n";
echo "========================================\n\n";

// List all users
echo "[1] ALL USERS:\n";
$users = User::all();
foreach ($users as $user) {
    $passStatus = $user->password ? 'SET' : 'EMPTY';
    echo "  ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Password: {$passStatus}\n";
}

// Check for duplicate emails
echo "\n[2] CHECKING FOR DUPLICATE EMAILS:\n";
$emailCounts = DB::table('users')
    ->select('email')
    ->groupBy('email')
    ->havingRaw('count(*) > 1')
    ->get()
    ->pluck('email')
    ->all();

if (count($emailCounts) > 0) {
    echo "  ⚠️  DUPLICATES FOUND:\n";
    foreach ($emailCounts as $email) {
        $userIds = DB::table('users')->where('email', $email)->pluck('id', 'name')->all();
        foreach ($userIds as $name => $id) {
            echo "    - ID: {$id}, Name: {$name}, Email: {$email}\n";
        }
    }
} else {
    echo "  ✓ No duplicates\n";
}

// Deepseol specific check
echo "\n[3] DEEPSEOL USER STATUS:\n";
$deepseol = User::where('name', 'Deepseol')->orWhere('email', 'like', '%deepseol%')->first();
if ($deepseol) {
    echo "  Found: ID {$deepseol->id}\n";
    echo "  Name: {$deepseol->name}\n";
    echo "  Email: {$deepseol->email}\n";
    echo "  Password: " . ($deepseol->password ? 'SET' : 'EMPTY/NULL') . "\n";
} else {
    echo "  ⚠️  Not found!\n";
}

echo "\n";
