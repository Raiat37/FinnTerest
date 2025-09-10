<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $rows = ExchangeRate::query()->get(['currency', 'rate']);

        $rates = [];
        foreach ($rows as $r) {
            $rates[strtoupper($r->currency)] = (float) $r->rate;
        }

        return response()->json(['rates' => $rates]);
    }


    public function upsert(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|string',
            'rate'     => 'required|numeric|min:0.000001', 
        ]);

        
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
