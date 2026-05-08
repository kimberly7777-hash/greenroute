<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:reset {email} {newPassword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $newPassword = $this->argument('newPassword');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $this->info("Password reset for {$email} to: {$newPassword}");
        return 0;
    }
}
