<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;


class PhoneForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        // return $request;

        try {
            //Get credentials from .env
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
            //  --------------------------------
            // create new Client
            $twilio = new Client($twilio_sid, $token);
            //  send code in sms massage
            $twilio->verify->v2->services($twilio_verify_sid)
                ->verifications
                ->create('+' . $request['phone_number'], "sms");
        } catch (\Throwable $exception) {
            return response()
                ->json(['Verify_code' => 'Please enter valid phone number'], 401);
        }
        return response()
            ->json(['Verify_code' => 'Verify code was sent']);
    }

    protected function CheckCode(Request $request)
    {
        // return $request;
        $user = User::where('phone',$request['phone_number'])->first();
        if (!$user) {
            return response([
                'message' => 'We can\'t find a user with that phone number'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'verification_code' => ['required', 'numeric'],
            'phone_number' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        /* Get credentials from .env */
        try {
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
            $twilio = new Client($twilio_sid, $token);
            $verification = $twilio->verify->v2->services($twilio_verify_sid)
                ->verificationChecks
                ->create($request['verification_code'], array('to' =>'+'. $request['phone_number']));
            if ($verification->valid) {

                return response()
                    ->json(['code' => 'The code is correct']);
            }
        } catch (\Throwable $th) {
            return response()
                ->json(['code_status' => 'The code not correct'], 402);
        }
    }

    public function reset(Request $request)
    {

        $user = User::where('phone', $request['phone_number'])->first();
        if ($user) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                
            ])->save();
            return response([
                'message' => 'Password reset successfully'
            ]);
        }else {
            return response([
                'message' => 'there is no user with this phone number'
            ], 401);
        }

    }
}
