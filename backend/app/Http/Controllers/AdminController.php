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

    // Reject user profile change
    public function rejectUserProfile($id)
    {
        $user = User::findOrFail($id);
        
        $user->profile_pending_approval = false;

        
        if (method_exists($user, 'setAttribute')) {
           
            try {
                if (array_key_exists('profile_rejected_at', $user->getAttributes())) {
                    $user->profile_rejected_at = now();
                }
            } catch (\Throwable $e) {
            }
        }

        $user->save();

        return response()->json(['message' => 'Profile rejected']);
    }
}
