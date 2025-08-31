<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SavingsRequest;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // 1) See list of users with pending profile changes
    public function listPendingUsers()
    {
    $users = User::where('profile_pending_approval', false)->where('role', 'user')->get();
        return response()->json($users);
    }

    // Approve user profile
    public function approveUserProfile($id)
    {
        $user = User::findOrFail($id);
        $user->profile_pending_approval = true;
        $user->save();

        return response()->json(['message' => 'Profile approved']);
    }

    // 2) Update currency exchange rate
    public function updateCurrencyRate(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|string|in:dollar,pound',
            'direction' => 'required|string|in:to,from',
            'rate' => 'required|numeric|min:0',
        ]);

        // Save in DB or config table
        CurrencyRate::updateOrCreate(
            ['currency' => $data['currency'], 'direction' => $data['direction']],
            ['rate' => $data['rate']]
        );

        return response()->json(['message' => 'Rate updated']);
    }

    // 3) Approve savings gateway request
    public function listSavingsRequests()
    {
        $requests = SavingsRequest::where('status', 'pending')->get();
        return response()->json($requests);
    }

    public function approveSavingsRequest($id, Request $request)
    {
        $saving = SavingsRequest::findOrFail($id);
        $saving->status = 'approved';
        $saving->redirect_url = $request->redirect_url;
        $saving->save();

        return response()->json(['message' => 'Savings request approved']);
    }
}
