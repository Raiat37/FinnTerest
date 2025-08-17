<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SavingsRequest;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     // Apply the admin check to all methods in this controller
    //     $this->middleware(function ($request, $next) {
    //         $adminId = auth()->user()->id ?? null; // Assuming authentication is set up
    //         if ($adminId !== 1) { // Replace '1' with the specific admin ID
    //             return response()->json(['error' => 'Unauthorized'], 403);
    //         }
    //         return $next($request);
    //     });
    // }
    // 1) See list of users with pending profile changes
    public function listPendingUsers()
    {
        $users = User::where('profile_pending_approval', true)->get();
        return response()->json($users);
    }

    // Approve user profile
    public function approveUserProfile($id)
    {
        $user = User::findOrFail($id);
        $user->profile_pending_approval = false;
        $user->save();

        return response()->json(['message' => 'Profile approved']);
    }

    // 2) Update currency exchange rate
    public function updateCurrencyRate(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string',
            'rate' => 'required|numeric'
        ]);

        DB::table('currency_rates')->updateOrInsert(
            ['currency_code' => $request->currency_code],
            ['rate' => $request->rate]
        );

        return response()->json(['message' => 'Currency rate updated']);
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
