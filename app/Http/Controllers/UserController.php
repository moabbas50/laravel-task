<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'verification_code' => rand(100000, 999999),
            'is_verified' => false,
        ]);

        Log::info('Verification code for ' . $user->phone . ': ' . $user->verification_code);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password) || !$user->verified) {
            return response()->json(['error' => 'Invalid credentials or unverified account'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'verification_code' => 'required|digits:6',
        ]);



        $user = User::where('phone', $request->phone)->first();

        if (!$user || $user->verification_code !== $request->code) {
            return response()->json(['error' => 'Invalid verification code'], 400);
        }

        $user->is_verified = true;
        $user->save();

        return response()->json(['message' => 'Account verified successfully']);
    }
}
