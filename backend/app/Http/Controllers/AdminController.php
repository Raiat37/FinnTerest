<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    // 1) See list of users with pending profile changes
    public function listPendingUsers()
    {
    $users = User::where('profile_pending_approval', true)->where('role', 'user')->get();
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
}
