<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Manager;
use App\Models\User;
use Illuminate\Support\Str;

echo "Syncing managers to users and assigning manager role:\n";

$managers = Manager::withTrashed()->get();
$count = 0;

foreach ($managers as $m) {
    $email = trim((string) $m->email);
    if ($email === '') {
        echo "- Skipping manager {$m->name} (no email)\n";
        continue;
    }

    $user = User::whereRaw('LOWER(email) = ?', [Str::lower($email)])->first();

    if (!$user) {
        echo "- No user found for manager {$m->name} <{$email}>\n";
        continue;
    }

    // Link manager to user if not linked
    if (!$m->user_id || $m->user_id !== $user->id) {
        $m->user_id = $user->id;
        $m->save();
        echo "✓ Linked manager {$m->name} to user {$user->email}\n";
    } else {
        echo "- Manager {$m->name} already linked to user {$user->email}\n";
    }

    // Assign manager role if missing
    if (!$user->hasRole('manager')) {
        $user->assignRole('manager');
        echo "  -> assigned 'manager' role to {$user->email}\n";
    }

    // ensure last_active_role set if missing
    if (!$user->last_active_role) {
        $user->update(['last_active_role' => 'manager']);
    }

    $count++;
}

echo "\nDone. Processed {$count} manager records.\n";
