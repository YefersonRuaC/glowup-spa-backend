<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('verification_token', $token)->first();

        if(!$user) {
            return response([
                'error' => 'Not valid verification token'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return response([
            'message' => 'Your account has been verified'
        ], Response::HTTP_OK);
    }

    public function sendVerificationEmail(User $user)
    {
        $token = Str::random(60);
        $user->verification_token = $token;
        $user->save();

        Mail::send('emails.verify', [
            'token' => $token
        ], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify your email address');
        });
    }
}
