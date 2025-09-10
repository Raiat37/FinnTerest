<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    // GET /api/admin/exchange-rates
    public function index()
    {
        // returns { rates: { USD: 110.5, GBP: 140.2 } } etc.
        $rows = ExchangeRate::query()->get(['currency', 'rate']);

        $rates = [];
        foreach ($rows as $r) {
            // return canonical uppercase currency codes (USD, GBP, EUR, INR)
            $rates[strtoupper($r->currency)] = (float) $r->rate;
        }

        return response()->json(['rates' => $rates]);
    }

    // POST /api/admin/exchange-rates
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|string',
            'rate'     => 'required|numeric|min:0.000001', // to Taka (e.g., 1 USD = 118.50 BDT)
        ]);

        // Accept various aliases and canonicalize to DB currency codes (USD, GBP, EUR, INR)
        $map = [
            'dollar' => 'USD', 'usd' => 'USD',
            'pound'  => 'GBP', 'gbp' => 'GBP',
            'eur'    => 'EUR', 'euro' => 'EUR',
            'inr'    => 'INR', 'rupee' => 'INR',
        ];

        $key = strtolower($data['currency']);
        $currency = $map[$key] ?? strtoupper($data['currency']);

        ExchangeRate::updateOrCreate(
            ['currency' => $currency],
            ['rate' => $data['rate']]
        );

        return response()->json(['message' => 'Rate saved']);
    }
}
