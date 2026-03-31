<?php
/**
 * Check users and duplicate emails
 */

use App\Models\User;

echo "=== ALL USERS ===\n\n";
$users = User::all();
foreach ($users as $user) {
    $passPreview = substr($user->password ?? 'EMPTY', 0, 20) . (strlen($user->password ?? '') > 20 ? '...' : '');
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Pass: {$passPreview}\n";
}

echo "\n\n=== DUPLICATE EMAILS ===\n\n";
$duplicates = User::selectRaw('email, count(*) as count')
    ->groupBy('email')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    foreach ($duplicates as $dup) {
        echo "Email '{$dup->email}' appears {$dup->count} times:\n";
        $matches = User::where('email', $dup->email)->get();
        foreach ($matches as $m) {
            echo "  - ID: {$m->id}, Name: {$m->name}\n";
        }
    }
} else {
    echo "No duplicate emails found!\n";
}

echo "\n";
