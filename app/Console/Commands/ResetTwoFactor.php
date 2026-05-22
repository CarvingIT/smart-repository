<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class ResetTwoFactor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:Reset2FA {email : The user email to reset 2FA for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset two-factor authentication for a user (clear TOTP secret so user can re-scan QR)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $this->info("Resetting two-factor authentication for user: {$user->email} (ID: {$user->id})");

        try {
            $user->disableTwoFactor();
        } catch (\Exception $e) {
            $this->error('Failed to disable two-factor authentication: ' . $e->getMessage());
            return 1;
        }

        $this->info('Two-factor authentication cleared. The user can now re-scan the QR code in their profile to reconfigure 2FA.');

        return 0;
    }
}
