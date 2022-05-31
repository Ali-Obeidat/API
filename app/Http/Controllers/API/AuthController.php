<?php

namespace App\Http\Controllers\API;

use App\Mail\ReferredEmail;
use App\Http\Controllers\API\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Location;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        if ($request['type'] == 'user') {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],

            ]);
            // return $validator->errors();
            if ($validator->fails()) {
                return response()->json($validator->errors(), 401);
            }

            // $encrypter = app(\Illuminate\Contracts\Encryption\Encrypter::class);
            // $ref = Cookie::get('referral');
            // if ($ref !== null) {
            //     $referred_by = $encrypter->decrypt($ref);
            //     $userRefe = User::find($referred_by);
            //     try {
            //         Mail::to($userRefe->email)->send(new ReferredEmail($userRefe));
            //     } catch (\Throwable $th) {
            //         //throw $th;
            //     }
            // } else {
            //     $referred_by = null;
            // }

            $user =  User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'phone' => $request['phone'],
                'type' => $request['type'],
                'country' => $request['country'],
                // 'referred_by' => $referred_by,
                // 'web'=>$data['web']?$data['web'] :null,
            ]);;

            $token = $user->createToken('auth_token')->plainTextToken;


            return response()
                ->json(['access_token' => $token, 'token_type' => 'Bearer', 'user_ifo' => $user]);
        } else {

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],

            ]);
            // return $validator->errors();
            if ($validator->fails()) {
                return response()->json($validator->errors(), 401);
            }

            // $encrypter = app(\Illuminate\Contracts\Encryption\Encrypter::class);
            // $ref = Cookie::get('referral');
            // if ($ref !== null) {
            //     $referred_by = $encrypter->decrypt($ref);
            //     $userRefe = User::find($referred_by);
            //     try {
            //         Mail::to($userRefe->email)->send(new ReferredEmail($userRefe));
            //     } catch (\Throwable $th) {
            //         //throw $th;
            //     }
            // } else {
            //     $referred_by = null;
            // }

            $user =  User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'phone' => $request['phone'],
                'type' => $request['type'],
                'country' => $request['country'],
                // 'referred_by' => $referred_by,
                // 'web'=>$data['web']?$data['web'] :null,
            ]);;

            $token = $user->createToken('auth_token')->plainTextToken;


            return response()
                ->json(['access_token' => $token, 'token_type' => 'Bearer', 'user_ifo' => $user]);
        }
    }

    public function login(Request $request)
    {

        // if (!Auth::attempt($request->only('email', 'password'))) {
        //     return response()
        //         ->json(['message' => 'The email or password that you\'ve entered is incorrect'], 401);
        // }
        $errors = [];
        $user = User::where('email', $request['email'])->first();
        // return $user ;
        if (!$user) {
            $errors = ['email' => 'Wrong Email'];
            return response()
                ->json(
                    $errors,
                    401
                );
        }
        // return $errors;
        if ($user && !Hash::check($request['password'], $user->password)) {
            $errors = ['password' => 'Wrong password'];
            return response()
                ->json(
                    $errors,
                    401
                );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['message' => 'Hi ' . $user->name . ', welcome to Hulul', 'access_token' => $token, 'token_type' => 'Bearer', 'user_ifo' => $user]);
    }

    // method for user logout and delete token
    public function logout()
    {

        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }

    public function getCurrentCountry()
    {

        $clientIP = request()->ip();
        // $data = Location::get($clientIP);

        $data = Location::get($clientIP);
        return response()
            ->json(['CurrentCountry' => $data]);
    }
}
