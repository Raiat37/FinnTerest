<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllotmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Allotment::create([
            'user_id' => 1, 
            'allotment' => 10000,
            'description' => 'Shopping',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'status' => 'active',
        ]);

        \App\Models\Allotment::create([
            'user_id' => 1, 
            'allotment' => 20000,
            'description' => 'Taxation',
            'start_date' => '2025-01-01',
            'end_date' => '2025-03-31',
            'status' => 'active',
        ]);

        \App\Models\Allotment::create([
            'user_id' => 1, 
            'allotment' => 5000,
            'description' => 'Cleaning',
            'start_date' => '2025-01-01',
            'end_date' => '2025-03-31',
            'status' => 'inactive',
        ]);
    }
}
