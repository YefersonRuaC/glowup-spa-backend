<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user =  User::where('email', $request->email)->first();

        if(!$user) {
            return response([
                'error' => 'Email does not exist'
            ], Response::HTTP_BAD_REQUEST);
        }

        $token = Str::random(60);
        $user->reset_token = $token;
        $user->save();

        Mail::send('emails.reset', [
            'token' => $token
        ], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Reset your password');
        });

        return response([
            'message' => 'Reset link sent to your email'
        ], Response::HTTP_OK);
    }

    public function verifyToken($token)
    {
        $user =  User::where('reset_token', $token)->first();

        if(!$user) {
            return response([
                'error' => 'Not valid token'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response([
            'message' => 'Token is valid'
        ], Response::HTTP_OK);
    }

    public function resetPassword(Request $request, $token)
    {
        $request->validate([
            // 'password' => ['required', 'min:8']
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $user = User::where('reset_token', $token)->first();

        if(!$user) {
            return response([
                'error' => 'Not valid token'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->save();

        return response([
            'message' => 'Password has been reset'
        ], Response::HTTP_OK);
    }
}
