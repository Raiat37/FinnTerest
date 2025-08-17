<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['login', 'register']);
    }

    public function login(Request $request)
    {
        // Login logic here 
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        // Registration logic here
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'job' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'expenditure' => 'nullable|numeric',
            'budget' => 'nullable|numeric',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        // Logout logic here
        $user = auth()->user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
