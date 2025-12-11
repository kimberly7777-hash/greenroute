<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Client;

class LinkClientsToUsers extends Command
{
    protected $signature = 'clients:link-users';
    protected $description = 'Link Client records to User records based on email address';

    public function handle()
    {
        $this->info('Starting to link clients to users...');
        
        $clients = Client::whereNull('user_id')->get();
        $count = 0;
        
        foreach ($clients as $client) {
            if (empty($client->email)) {
                continue;
            }
            
            $user = User::where('email', $client->email)->first();
            
            if ($user) {
                $client->user_id = $user->id;
                $client->save();
                
                // Also ensure user has client role
                if (!$user->hasRole('client')) {
                    $user->assignRole('client');
                }
                
                $this->info("Linked Client: {$client->name} ({$client->email}) to User ID: {$user->id}");
                $count++;
            }
        }
        
        $this->info("Completed! Linked {$count} clients.");
        return 0;
    }
}
