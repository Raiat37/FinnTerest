<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Models\Admin;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required','email'],
        'password' => ['required','string'],
    ]);

    $email = $credentials['email'];
    $pwd   = $credentials['password'];

    // Look both up (you can prefer one over the other if you want)
    $user  = User::where('email', $email)->first();
    $admin = Admin::where('email', $email)->first();

    $account = null;
    $role    = null;

    if ($user && Hash::check($pwd, $user->password)) {
        $account = $user;
        $role = 'user';
    } elseif ($admin && Hash::check($pwd, $admin->password)) {
        $account = $admin;
        $role = 'admin';
    }

    if (! $account) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $account->createToken('api')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'role'    => $role,
        'token'   => $token,
        'user'    => $account->makeHidden(['password', 'remember_token']),
    ]);
}

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => 'required|string|min:8|confirmed',
            'job'         => 'nullable|string|max:255',
            'salary'      => 'nullable|numeric',
            'expenditure' => 'nullable|numeric',
            'budget'      => 'nullable|numeric',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        // Revoke only the current token:
        $request->user()->currentAccessToken()?->delete();

        // Or revoke ALL tokens:
        // $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
