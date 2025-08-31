<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'User1',
            'email' => 'user1@example.com',
            'password' => Hash::make('userpass'),
            'role' => 'user',
            'job' => 'Software Engineer',
            'salary' => 60000,
            'expenditure' => 20000,
            'budget' => 40000,
            'profile_pending_approval' => true,
        ]);
        User::create([
            'name' => 'User2',
            'email' => 'user2@example.com',
            'password' => Hash::make('userpass'),
            'role' => 'user',
            'job' => 'Product Manager',
            'salary' => 80000,
            'expenditure' => 30000,
            'budget' => 50000,
            'profile_pending_approval' => true,
        ]);
        User::create([
            'name' => 'User3',
            'email' => 'user3@example.com',
            'password' => Hash::make('userpass'),
            'role' => 'user',
            'job' => 'UX Designer',
            'salary' => 70000,
            'expenditure' => 25000,
            'budget' => 45000,
            'profile_pending_approval' => false,
        ]);
        User::create([
            'name' => 'Admin1',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpass'),
            'role' => 'admin',
            'profile_pending_approval' => false,
        ]);
    }

}
