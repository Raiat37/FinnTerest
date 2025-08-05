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
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'job' => 'Software Engineer',
            'salary' => 60000,
            'expenditure' => 20000,
            'budget' => 40000,
        ]);
        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'job' => 'Product Manager',
            'salary' => 80000,
            'expenditure' => 30000,
            'budget' => 50000,
        ]);
        User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'job' => 'UX Designer',
            'salary' => 70000,
            'expenditure' => 25000,
            'budget' => 45000,
        ]);
    }

}
