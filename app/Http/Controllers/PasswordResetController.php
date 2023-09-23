<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;

class PasswordResetController extends Controller
{
    public function getResetEmail(Request $request)
    {
        //Validate Email
        $request->validate([
            'email' => 'required|email'
        ]);
        $email = $request->email;
        $user = User::where('email', $request->email)->first();

        //Check if user exists or not
        if (!$user) {
            return response()->json([
                'message' => 'Email does not exist'
            ], 404);
        }

        //Generate Token
        $token = Str::random(60);

        //Saving data to password reset table
        PasswordReset::create([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        //Sending email with reset view 
        Mail::send('reset', compact('token'), function (Message $message) use ($email) {
            $message->subject('Reset Your Email');
            $message->to($email);
        });

        //send the email response
        return response()->json([
            'message' => 'Email Sent Successfully... Check Mail'
        ], 200);
    }

    public function reset(Request $request, $token)
    {
        //make the token valid for 1 minute
        $formatedtime = Carbon::now()->subMinute(5)->toDateTimeString();
        PasswordReset::where('created_at' , '<=' , $formatedtime)->delete();

        $request->validate([
            'password' => 'required|confirmed'
        ]);

        $resettoken = PasswordReset::where('token', $token)->first();
        if (!$resettoken) {
            return response()->json([
                'message' => 'Token Invalid or Expired'
            ], 404);
        }

        $user = User::where('email', $resettoken->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User Not Found'
            ], 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $user->email)->delete();
        return response()->json([
            'message' => 'Password Reset Successfully'
        ], 200);
    }
}
