<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'debug:reset-password {user_id} {password}';
    protected $description = 'Reset a user password directly';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $newPassword = $this->argument('password');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User ID {$userId} not found");
            return 1;
        }

        try {
            $user->update(['password' => Hash::make($newPassword)]);
            $this->info("✓ Password reset successfully!");
            $this->line("User: {$user->name}");
            $this->line("Email: {$user->email}");
            $this->line("New Password: {$newPassword}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
