<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list {--type= : Filter by user type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        $query = User::query();

        if ($type) {
            $query->where('user_type', $type);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Type', 'Status', 'Created'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->user_type,
                    $user->status ?? 'null',
                    $user->created_at->format('Y-m-d H:i'),
                ];
            })
        );
    }
}
