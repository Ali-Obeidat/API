<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;


class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
            // get user information
            $user = Socialite::driver('google')->user();

            $finduser = User::where('email', $user->email)->first();
            // check if the user has account or not
            if ($finduser) {
                // login to his account
                $token = $finduser->createToken('auth_token')->plainTextToken;
                return response()
                    ->json(['message' => 'Hi ' . $finduser->name . ', welcome to Hulul', 'access_token' => $token, 'token_type' => 'Bearer', 'user_ifo' => $finduser]);
            } else {
                // if not create new account
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,

                    'auth_type' => 'google',
                    'password' => Hash::make($user->id)
                ]);
                $token = $newUser->createToken('auth_token')->plainTextToken;
                return response()
                    ->json(['message' => 'Hi ' . $newUser->name . ', welcome to Hulul', 'access_token' => $token, 'token_type' => 'Bearer', 'user_ifo' => $newUser]);
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
