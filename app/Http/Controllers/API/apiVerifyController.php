<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Validator;


class apiVerifyController extends Controller
{

    // Function to check if the code correct or not
    protected function verify(Request $request)
    {

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
                ->create($request['verification_code'], array('to' => $request['phone_number']));
            if ($verification->valid) {

                $user = User::where('phone', $request['phone_number'])->update(['phone_verified_at' => Carbon::now()]);
                return response()
                    ->json(['Verify_Phone' => 'Phone number was Verified']);
            }
        } catch (\Throwable $th) {


            return response()
                ->json(['code_status' => 'The code not correct'], 401);
        }
    }

    // Function to send verify code to phone number
    public function verifyPhone(Request $request)
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
}
