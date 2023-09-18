<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed',
        ]);
        if(User::where('email',$request->email)->first()){
            return response()->json([
                'message'=>'Email already exists'
            ],409);          
        }
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        $token=$user->createToken($request->email)->plainTextToken;
        return response()->json([
            'token'=>$token,
            'message'=>'Registration Successful'
        ],200);
    }
}
