<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {

        $request->validate([
                'email' => 'required|email',
            ]);
   
            $status = Password::sendResetLink(
                $request->only('email')
            );
            if ($status == Password::RESET_LINK_SENT) {
                return [
                    'status' => __($status)
                ];
            }else{
                return response()->json(["email" =>trans($status)],401);
                
            }
      
    }

   
}
