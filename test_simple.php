<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('email', 'newhire@vnb.local')->first();
$token = $user->createToken('test_token_' . time())->plainTextToken;

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

$body = json_decode($response, true);

echo "Status: {$httpcode}\n\n";
if ($body) {
    echo "Success: " . ($body['success'] ? 'YES' : 'NO') . "\n";
    if (isset($body['message'])) echo "Message: {$body['message']}\n";
    if (isset($body['deadline'])) echo "Deadline: {$body['deadline']}\n";
    if (isset($body['data'])) {
        echo "Plan ID: {$body['data']['id']}\n";
        echo "Plan Title: {$body['data']['title']}\n";
        echo "Plan Status: {$body['data']['status']}\n";
        echo "Items Count: " . count($body['data']['items'] ?? []) . "\n";
        if (isset($body['data']['items']) && count($body['data']['items']) > 0) {
            echo "First Item: {$body['data']['items'][0]['activity_title']}\n";
        }
    }
} else {
    echo "Invalid JSON response\n";
}
