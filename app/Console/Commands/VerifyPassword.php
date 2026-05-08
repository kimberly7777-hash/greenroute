<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class VerifyPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:verify {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify if a password matches for a given email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        if (Hash::check($password, $user->password)) {
            $this->info("Password is correct for {$email}");
            return 0;
        } else {
            $this->error("Password is incorrect for {$email}");
            return 1;
        }
    }
}
