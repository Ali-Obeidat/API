<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

class apiEmailVerificationController extends Controller
{
    // Function send Verification Email to the user
    public function sendVerificationEmail(Request $request)
    {
        // Find the user by id
        $user= User::find($request['user_id']);
        // check if the user email verified or not
        if ($user->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified'
            ];
        }
        // if not, send email to the user 
        $user->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }

    // Function to verify user email 
    public function verify(Request $request)
    {
        $user= User::find($request['id']);
        // check if the user email verified or not
        if ($user->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified'
            ];
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return [
            'message'=>'Email has been verified'
        ];
    }
}