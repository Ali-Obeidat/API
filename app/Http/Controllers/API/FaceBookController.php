<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Exception;
use App\Http\Controllers\API\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class FaceBookController extends Controller
{
  /**
   * Login Using Facebook
   */

  // Function to user facebook driver
  public function loginUsingFacebook()
  {
    return Socialite::driver('facebook')->redirect();
  }

  public function callbackFromFacebook()
  {
    try {
      // get user information
      $user = Socialite::driver('facebook')->user();
      // check if the user has email or not
      if (!empty($user->email)) {
        $finduser = User::where('email', $user->email)->first();
      } else {
        // if not get the user by facebook id
        $finduser = User::where('facebook_id', $user->facebook_id)->first();
      }
      // check if the user has account or not
      if ($finduser) {
        // login to his account

        $token = $finduser->createToken('auth_token')->plainTextToken;
        return response()
          ->json(['message' => 'Hi ' . $finduser->name . ', welcome to Hulul', 'access_token' => $token, 'token_type' => 'Bearer', 'user_ifo' => $finduser]);
        return redirect('/demos');
      } else {
        // if not create new account
        $newUser = User::create([
          'name' => $user->name,
          'email' => $user->email,
          'facebook_id' => $user->id,
          'phone' => 00,
          'auth_type' => 'facebook',
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
