<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['currency' => 'USD', 'rate' => 118.50], // 1 USD = 118.50 BDT
            ['currency' => 'GBP', 'rate' => 154.20], // 1 GBP = 154.20 BDT
            ['currency' => 'EUR', 'rate' => 129.10], // 1 EUR = 129.10 BDT
            ['currency' => 'INR', 'rate' => 1.43],   // 1 INR = 1.43 BDT
        ];

        foreach ($rates as $r) {
            ExchangeRate::updateOrCreate(
                ['currency' => $r['currency']],
                ['rate' => $r['rate']]
            );
        }
    }
}
