<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $avatar_path = Storage::disk('public')->put('users', $avatar);
            $request->avatar = $avatar_path;
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' =>Hash::make($request->password),
            'avatar' => $request->avatar
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->avatar = url(Storage::url($user->avatar));
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'User logged in successfully',
            'user' => $user
        ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete(); // Revoke all tokens...
        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }

    public function me(){
        return response()->json(auth()->user());
    }
}
