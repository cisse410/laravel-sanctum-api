<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        $token = $user->createToken('Token de ' . $user->name)->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);


        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Erreur!! Verifiez vos identifiants',
            ]);
        }
        $user = User::where('email', $fields['email'])->first();
    }

    public function logout()
    {
        Auth::user()->currentAccessToken->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}