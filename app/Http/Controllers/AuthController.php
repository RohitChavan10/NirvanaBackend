<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
{
    // Only admin can register new users
    if ($request->user()->user_type !== 'admin') {
        return response()->json([
            'message' => 'Unauthorized: Only admin can create users.'
        ], 403);
    }

    // Validate input
    $validated = $request->validate([
        'username'        => 'required|string|max:255|unique:users,username',
        'user_firstName'  => 'required|string|max:255',
        'user_lastName'   => 'required|string|max:255',
        'email_id'        => 'required|string|email|max:255|unique:users,email_id',
        'password'        => 'required|string|min:8',
        'user_type'       => 'required|string', // e.g. admin, user, manager etc.
    ]);

    // Create user
    $user = User::create([
        'username'       => $validated['username'],
        'user_firstName' => $validated['user_firstName'],
        'user_lastName'  => $validated['user_lastName'],
        'email_id'       => $validated['email_id'],
        'password'       => Hash::make($validated['password']),
        'user_type'      => $validated['user_type'],
    ]);

    return response()->json([
        'message' => 'User created successfully.',
        'user'    => $user
    ], 201);
}
public function login(Request $request)
{
    // Validate input
    $credentials = $request->validate([
        'email_id' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Find user by email_id
    $user = User::where('email_id', $credentials['email_id'])->first();

    // Check if user exists and password matches
    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return response()->json([
            'message' => 'Invalid email or password.',
        ], 401);
    }
    // Create Sanctum token
    $token = $user->createToken('api-token')->plainTextToken;

    // (Optional: Later we will issue Sanctum tokens here)
    return response()->json([
        'message' => 'Login successful.',
        'user' => $user,
        'token'   => $token,
    ], 200);
}



}
