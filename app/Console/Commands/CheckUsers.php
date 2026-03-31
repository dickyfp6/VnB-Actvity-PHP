<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUsers extends Command
{
    protected $signature = 'debug:check-users';
    protected $description = 'Check users and diagnose email issues';

    public function handle()
    {
        $this->info('========================================');
        $this->info('USER AND EMAIL DIAGNOSTICS');
        $this->info('========================================');

        // List all users
        $this->info("\n[1] ALL USERS:");
        $users = User::all();
        foreach ($users as $user) {
            $passStatus = $user->password ? 'SET (' . substr($user->password, 0, 15) . '...)' : 'EMPTY/NULL';
            $this->line("  ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Pass: {$passStatus}");
        }

        // Check for duplicate emails
        $this->info("\n[2] DUPLICATE EMAILS:");
        $duplicates = DB::table('users')
            ->select('email')
            ->groupBy('email')
            ->havingRaw('count(*) > 1')
            ->pluck('email');

        if ($duplicates->count() > 0) {
            $this->warn("  ⚠️  DUPLICATES FOUND:");
            foreach ($duplicates as $email) {
                $matches = User::where('email', $email)->select('id', 'name')->get();
                foreach ($matches as $m) {
                    $this->line("    - ID: {$m->id}, Name: {$m->name}, Email: {$email}");
                }
            }
        } else {
            $this->info("  ✓ No duplicate emails");
        }

        // Deepseol specific check
        $this->info("\n[3] DEEPSEOL USER:");
        $deepseol = User::where('name', 'Deepseol')
            ->orWhere('email', 'like', '%deepseol%')
            ->first();

        if ($deepseol) {
            $this->info("  ✓ Found:");
            $this->line("    ID: {$deepseol->id}");
            $this->line("    Name: {$deepseol->name}");
            $this->line("    Email: {$deepseol->email}");
            $this->line("    Password: " . ($deepseol->password ? 'SET' : 'EMPTY/NULL'));
        } else {
            $this->error("  ✗ Not found!");
        }

        $this->info("\n");
    }
}
