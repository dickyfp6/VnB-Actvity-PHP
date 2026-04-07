<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PASSWORD GENERATION DIAGNOSTIC ===\n\n";

// Check all employees
$all = App\Models\Employee::all();
echo "Total employees in DB: " . count($all) . "\n\n";

foreach ($all as $emp) {
    echo "---\n";
    echo "ID: {$emp->id} | Name: {$emp->name} | NIP: {$emp->employee_number}\n";
    
    // Check user account
    $user = App\Models\User::where('employee_id', $emp->id)->first();
    if ($user) {
        echo "  User Email: {$user->email}\n";
        echo "  Temp Password Encrypted: " . ($user->temp_password_encrypted ? 'YES' : 'NO') . "\n";
        if ($user->temp_password_encrypted) {
            try {
                $decrypted = Crypt::decryptString($user->temp_password_encrypted);
                echo "  Decrypted Password: {$decrypted}\n";
            } catch (\Exception $e) {
                echo "  Cannot decrypt\n";
            }
        }
    }
    
    // Simulate password generation for this employee
    $name = trim((string)$emp->name);
    $firstName = preg_split('/\s+/', $name)[0] ?? '';
    $firstName = strtolower(preg_replace('/[^a-z0-9]/', '', $firstName));
    if ($firstName === '') $firstName = 'user';
    
    $nipDigits = preg_replace('/\D+/', '', (string)($emp->employee_number ?? '')) ?? '';
    $suffix = $nipDigits === '' ? '00' : str_pad(substr($nipDigits, -2), 2, '0', STR_PAD_LEFT);
    
    echo "  Expected Password: " . $firstName . $suffix . "\n";
}
