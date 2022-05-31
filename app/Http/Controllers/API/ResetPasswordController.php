<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Support\Str;


class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $email = User::where('email',$request['email'])->first();
        
        
        if ($email) {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();
    
                    $user->tokens()->delete();
    
                    event(new PasswordReset($user));
                }
            );
            // return  $status;
            if ($status == Password::PASSWORD_RESET) {
                return response([
                    'message' => 'Password reset successfully'
                ]);
            }
    
            return response([
                'message' => 'Pleas go back and ask to send reset password email again'
            ], 500);
        }else {
            return response([
                'message' => 'We can\'t find a user with that email address'
            ], 401);
        }
        
    }
}
