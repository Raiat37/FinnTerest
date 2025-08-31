<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\SavingsApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class SavingsGatewayController extends Controller
{
    // GET /savings/applications
    public function index(Request $request)
    {
        $apps = SavingsApplication::with('bank')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($apps);
    }

    // POST /savings/applications
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'bank_id'      => ['required', Rule::exists('banks','id')],
            'mode'         => ['required', Rule::in(['auto','manual'])],
            'amount'       => ['nullable','numeric','min:0.01'],
            'period_month' => ['nullable','date_format:Y-m-d'], // expect first day of month; fallback to current month
        ]);

        $bank = Bank::findOrFail($validated['bank_id']);

        // Determine the month bucket
        $period = !empty($validated['period_month'])
            ? Carbon::parse($validated['period_month'])->startOfMonth()
            : now()->startOfMonth();

        // Compute amount
        if ($validated['mode'] === 'auto') {
            $salary = (float) ($user->salary ?? 0);
            $budget = (float) ($user->budget ?? 0);
            $auto = max(0, $salary - $budget);

            if ($auto <= 0) {
                return response()->json([
                    'message' => 'No positive surplus for this month. Use manual mode to set a value.'
                ], 422);
            }
            $amount = $auto;
        } else {
            // manual mode
            if (!isset($validated['amount'])) {
                return response()->json(['message' => 'Amount is required for manual mode.'], 422);
            }
            $amount = (float) $validated['amount'];
        }

        // Prevent duplicate application for same month:
        // - if there is a pending/approved application -> block
        // - if there is a rejected application -> reuse/update it (avoid unique constraint)
        $existing = SavingsApplication::where('user_id', $user->id)
            ->whereDate('period_month', $period)
            ->first();

        if ($existing) {
            if (in_array($existing->status, ['pending', 'approved'])) {
                return response()->json([
                    'message' => 'You already have a savings application for this month.'
                ], 422);
            }

            // Existing application was rejected (or other non-blocking status): update it to a new pending application
            $existing->update([
                'bank_id'      => $bank->id,
                'amount'       => $amount,
                'mode'         => $validated['mode'],
                'status'       => 'pending',
                'approved_by'  => null,
                'approved_at'  => null,
                'period_month' => $period->format('Y-m-d'),
            ]);

            $app = $existing->fresh();
        } else {
            $app = SavingsApplication::create([
                'user_id'      => $user->id,
                'bank_id'      => $bank->id,
                'amount'       => $amount,
                'mode'         => $validated['mode'],
                'status'       => 'pending',
                'period_month' => $period->format('Y-m-d'),
            ]);
        }

        return response()->json([
            'message' => 'Savings application submitted.',
            'application' => $app->load('bank')
        ], 201);
    }
}
