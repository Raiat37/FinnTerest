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
        // returns { rates: { dollar: 110.5, pound: 140.2 } } etc.
        $rows = ExchangeRate::query()->get(['currency', 'rate']);

        $rates = [];
        foreach ($rows as $r) {
            // normalize currency keys to lowercase
            $rates[strtolower($r->currency)] = (float) $r->rate;
        }

        return response()->json(['rates' => $rates]);
    }

    // POST /api/admin/exchange-rates
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|string|in:dollar,pound,usd,gbp',
            'rate'     => 'required|numeric|min:0.000001', // to Taka (e.g., 1 USD = 118.50 BDT)
        ]);

        // map aliases -> canonical keys
        $map = ['usd' => 'dollar', 'gbp' => 'pound'];
        $key = strtolower($data['currency']);
        $currency = $map[$key] ?? $key;

        ExchangeRate::updateOrCreate(
            ['currency' => $currency],
            ['rate' => $data['rate']]
        );

        return response()->json(['message' => 'Rate saved']);
    }
}
