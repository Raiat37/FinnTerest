<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users and their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $this->table([
            'ID', 'Name', 'Email', 'Role', 'Job', 'Salary', 'Expenditure', 'Budget', 'Profile Pending Approval'
        ], $users->map(function ($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->job,
                $user->salary,
                $user->expenditure,
                $user->budget,
                $user->profile_pending_approval ? 'Yes' : 'No',
            ];
        })->toArray());
    }
}
