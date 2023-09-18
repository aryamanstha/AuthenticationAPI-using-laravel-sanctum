<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);
        if (User::where('email', $request->email)->first()) {
            return response()->json([
                'message' => 'Email already exists'
            ], 409);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken($request->email)->plainTextToken;
        return response()->json([
            'token' => $token,
            'message' => 'Registration Successful'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'token' => $user->createToken($request->email)->plainTextToken,
                'message' => 'Login Successful'
            ], 200);
        }
        return response()->json([
            'message' => 'The Credentials are incorrect',
        ], 401);
    }
    public function logout()
    {
        $user=Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Logout Successful'
        ], 200);
    }

    public function getLoggedUser(){
        $data=Auth::user();
        return response()->json([
            ' logged_in data'=>$data,
            'message'=>'Logged In User data',
        ],200);
    }
}
