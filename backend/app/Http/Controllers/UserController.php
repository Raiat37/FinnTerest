<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SavingsRequest;

class UserController extends Controller
{
    // NEW: View own profile
    public function showProfile(Request $request)
    {
        $user = $request->user()->makeHidden(['password', 'remember_token']);
        return response()->json([
            'user' => $user
        ]);
    }

    // 1) Edit profile (pending approval)
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $user->update($request->only([
            'name', 'email', 'job', 'salary', 'expenditure'
        ]));

        $user->profile_pending_approval = true;
        $user->save();

        return response()->json(['message' => 'Profile update pending approval']);
    }

    // 2) Submit savings request
    public function submitSavingsRequest(Request $request)
    {
        $request->validate([
            'amount'  => 'required|numeric|min:1',
            'bank_id' => 'required|exists:banks,id'
        ]);

        SavingsRequest::create([
            'user_id' => Auth::id(),
            'amount'  => $request->amount,
            'bank_id' => $request->bank_id,
            'status'  => 'pending'
        ]);

        return response()->json(['message' => 'Savings request submitted']);
    }
    // NEW: Update only budget without changing profile_pending_approval state
    public function updateBudget(Request $request)
    {
        $request->validate([
            'budget' => 'required|numeric|min:0'
        ]);

        $user = Auth::user();
        $user->budget = $request->budget;
        $user->save();

        return response()->json(['message' => 'Budget updated successfully']);
    }
}
