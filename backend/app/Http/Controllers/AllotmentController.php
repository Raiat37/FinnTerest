<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allotment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class AllotmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'allotment' => 'required|integer|min:1',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $data = $validated->validated();
        $user = User::find($data['user_id']);

        // Total allocated so far for this user
        $totalAllotted = Allotment::where('user_id', $user->id)->sum('allotment');

        // Check if this new allotment would exceed budget
        $remainingBudget = $user->budget - $totalAllotted;

        if ($remainingBudget <= 0) {
            return response()->json([
                'error' => 'Budget already fully used. No remaining balance.'
            ], 400);
        }

        if ($data['allotment'] > $remainingBudget) {
            return response()->json([
                'error' => 'Insufficient budget. Remaining: ' . $remainingBudget . ' units.'
            ], 400);
        }

        // Create allotment
        $allotment = Allotment::create([
            'user_id' => $data['user_id'],
            'allotment' => $data['allotment'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'active' => 'active',
        ]);

        return response()->json([
            'message' => 'Allotment created successfully.',
            'allotment' => $allotment,
            'remaining_budget' => $remainingBudget - $data['allotment'],
        ], 201);
    }
    public function index()
    {
        return Allotment::with('user')->get();
    }

}
