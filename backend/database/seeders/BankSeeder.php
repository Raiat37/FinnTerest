<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['name' => 'BRAC Bank', 'website_url' => 'https://www.bracbank.com/', 'is_active' => true],
            ['name' => 'Dutch-Bangla Bank', 'website_url' => 'https://www.dutchbanglabank.com/', 'is_active' => true],
            ['name' => 'City Bank', 'website_url' => 'https://www.thecitybank.com/', 'is_active' => true],
        ];

        foreach ($banks as $b) {
            Bank::updateOrCreate(['name' => $b['name']], $b);
        }
    }
}
