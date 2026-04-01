<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Support\Facades\Hash;

// Get Manager Dicky
$dickyManager = Manager::find(1);
$dickyUser = User::find(6);

// Get Deepseol employee
$deepseol = Employee::find(2);

if (!$dickyManager || !$dickyUser || !$deepseol) {
    echo "Data not found!\n";
    exit(1);
}

echo "=== CURRENT STATE ===\n";
echo "Manager Dicky\n";
echo "- Name: " . $dickyManager->name . "\n";
echo "- Email: " . $dickyManager->email . "\n";
echo "- User ID: " . $dickyManager->user_id . "\n";
echo "- User Email: " . $dickyUser->email . "\n\n";

echo "Employee Deepseol\n";
echo "- Name: " . $deepseol->name . "\n";
echo "- Email: " . $deepseol->email . "\n";
echo "- User ID: " . ($deepseol->user_id ?? 'NULL') . "\n\n";

// Option: Change Manager Dicky's email
// Using format: firstname.lastname@domain
$newDickyEmail = 'dicky.febri@tugasakhir.local';

echo "=== RECOMMENDED FIX ===\n";
echo "Step 1: Change Manager Dicky's email\n";
echo "FROM: " . $dickyManager->email . "\n";
echo "TO: " . $newDickyEmail . "\n\n";

// Check if new email already exists
$existingUser = User::where('email', $newDickyEmail)->first();
if ($existingUser) {
    echo "ERROR: Email " . $newDickyEmail . " already exists!\n";
    exit(1);
}

$existingManager = Manager::where('email', $newDickyEmail)->first();
if ($existingManager) {
    echo "ERROR: Manager with email " . $newDickyEmail . " already exists!\n";
    exit(1);
}

// Update emails
$dickyManager->update(['email' => $newDickyEmail]);
$dickyUser->update(['email' => $newDickyEmail]);

echo "SUCCESS: Manager Dicky's email changed to " . $newDickyEmail . "\n\n";

echo "Step 2: Create user account for Deepseol\n";

// Create user for Deepseol
$deepseolUser = User::create([
    'name' => $deepseol->name,
    'email' => $deepseol->email, // tugasakhirdicky036@gmail.com
    'password' => Hash::make('deepseol890'),
    'status' => 'active',
    'email_verified_at' => now(),
    'employee_id' => $deepseol->id,
]);

$deepseolUser->assignRole('new_hire');
$deepseol->update(['user_id' => $deepseolUser->id]);

echo "SUCCESS: User account created for Deepseol!\n";
echo "- User ID: " . $deepseolUser->id . "\n";
echo "- Email: " . $deepseolUser->email . "\n";
echo "- Password: deepseol890\n";
echo "- Role: new_hire\n\n";

echo "=== FINAL STATE ===\n";
echo "Manager Dicky\n";
echo "- Email: " . $dickyManager->fresh()->email . "\n";
echo "- User Email: " . $dickyUser->fresh()->email . "\n\n";

echo "Employee Deepseol\n";
echo "- Email: " . $deepseol->fresh()->email . "\n";
echo "- User ID: " . $deepseol->fresh()->user_id . "\n";
echo "- User Email: " . $deepseolUser->fresh()->email . "\n";

echo "\n✅ All fixed!\n";
