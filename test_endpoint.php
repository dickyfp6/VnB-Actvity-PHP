<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

// Create test user if not exists
$user = User::firstOrCreate(
    ['email' => 'newhire@vnb.local'],
    [
        'name' => 'New Hire Test',
        'password' => Hash::make('password123'),
        'status' => 'active',
        'employee_id' => 2, // Deepseol
    ]
);

// Assign new_hire role
$user->syncRoles('new_hire');

echo "✓ User: {$user->email}\n";
echo "✓ Employee ID: {$user->employee_id}\n";
echo "✓ Role: new_hire\n";
echo "✓ Password: password123\n";
echo "\nTest API call...\n";

// Create a sanctum token for API testing
$token = $user->createToken('test_token')->plainTextToken;
echo "\nToken: {$token}\n";

// Make HTTP request to test endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/vnb-plans/new-hire');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
]);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\nResponse Status: {$httpcode}\n";
echo "Response:\n";
echo $response . "\n";
