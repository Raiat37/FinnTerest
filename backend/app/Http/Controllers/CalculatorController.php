<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExchangeRate;

class CalculatorController extends Controller
{
    // 1) Simple BODMAS calculator
    
    public function calculate(Request $request)
    {
        $request->validate([
            'expression' => ['required','string','max:200'],
        ]);

        $expr = trim((string) $request->input('expression'));

        // Allow only digits, spaces, decimal dot, parentheses, and + - * / 
        // Use ~ as the regex delimiter to avoid / escaping issues.
        if (!preg_match('~^[0-9+\-\*\/().\s]+$~', $expr)) {
            return response()->json(['error' => 'Invalid characters in expression'], 422);
        }

        try {
            // Convert PHP warnings (e.g., division by zero) to exceptions
            set_error_handler(function ($errno, $errstr) {
                throw new \ErrorException($errstr, $errno);
            });

            // Evaluate (BODMAS handled by PHP expression parsing)
            $result = eval('return (' . $expr . ');');

            restore_error_handler();

            if (!is_numeric($result) || !is_finite((float)$result)) {
                return response()->json(['error' => 'Invalid calculation result'], 422);
            }

            return response()->json(['result' => $result]);
        } catch (\Throwable $e) {
            restore_error_handler();
            return response()->json([
                'error' => 'Calculation error: ' . $e->getMessage(),
            ], 422);
        }
    }

    // 2) Currency converter
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'from'   => 'required|string|in:BDT,USD,GBP,EUR,INR',
            'to'     => 'required|string|in:BDT,USD,GBP,EUR,INR',
        ]);

        $amount = $request->input('amount');
        $from   = strtoupper($request->input('from'));
        $to     = strtoupper($request->input('to'));

        if ($from === $to) {
            return response()->json(['result' => $amount]);
        }

        // Get exchange rates (BDT per 1 unit foreign currency)
        $rates = ExchangeRate::pluck('rate', 'currency')->toArray();

        // Helper: get BDT value of 1 unit
        $bdtPerUnit = fn($code) => $code === 'BDT' ? 1 : ($rates[$code] ?? null);

        $fromRate = $bdtPerUnit($from);
        $toRate   = $bdtPerUnit($to);

        if (!$fromRate || !$toRate) {
            return response()->json(['error' => 'Missing exchange rate'], 422);
        }

        // Convert: first to BDT, then to target
        $inBDT  = ($from === 'BDT') ? $amount : $amount * $fromRate;
        $result = ($to === 'BDT') ? $inBDT : $inBDT / $toRate;

        return response()->json(['result' => round($result, 2)]);
    }

    // Admin: update or set rate
    public function setRate(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|in:USD,GBP,EUR,INR',
            'rate'     => 'required|numeric|min:0',
        ]);

        ExchangeRate::updateOrCreate(
            ['currency' => strtoupper($request->currency)],
            ['rate' => $request->rate]
        );

        return response()->json(['message' => 'Rate updated']);
    }
}
