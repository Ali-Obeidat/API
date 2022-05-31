<?php

namespace App\Http\Controllers\API;

use App\Models\PendingRealAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class apiRealAccountsController extends Controller
{
    // Function to create Real Account request
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leverage' => "required",
            'currency' => "required",

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $loginUser = User::find($request['user_id']);
        $basicInfo = $loginUser->information;
        $FinancialProfile = $loginUser->quc;
        $Documents = $loginUser->documents;
        $emailVerified = $loginUser->email_verified_at;
        $phoneVerified = $loginUser->phone_verified_at;
         // check if user has filed all profile information 
        if (
            !$basicInfo->exists() || $emailVerified == null
            || $phoneVerified == null || !$FinancialProfile->exists()
            || empty($Documents[0])
        ) {
            if (!$basicInfo->exists()) {
                return response()->json('Please fill your profile info first');
            }elseif ($emailVerified == null) {
                return response()->json('Please verify your email first');
            }elseif ($phoneVerified == null) {
                return response()->json('Please verify your phone first');
            }elseif (!$FinancialProfile->exists()) {
                return response()->json('Please fill your Financial profile first');
            }elseif (empty($Documents[0])) {
                return response()->json('Please upload your Documents first');
            }

        } 
        // get the user be id and get his basic information
        
        $basicInfo = $loginUser->information;
        // -------------------------------------------------

        // create new record in pending_real_accounts table
        $userData = new PendingRealAccount();
        $userData->name = $basicInfo->full_name;
        $userData->login = 0;
        $userData->email = $basicInfo->email;
        $userData->group = ('preliminary');
        $userData->leverage = (($request->leverage));
        $userData->account_type = "Real";
        $userData->currency = (($request->currency));
        $userData->phone = ($basicInfo->phone);
        $userData->address = ($basicInfo->adders);
        $userData->city = ($basicInfo->city);
        $userData->state = ($basicInfo->state);
        $userData->country = ($basicInfo->citizenship);
        $userData->zipcode = ($basicInfo->zip_code);
        $userData->password = ($request->password);
        $userData->invest_password = ($request->password);
        $userData->phone_password = ($request->password);
        $userData->account_status = "pending";
        $userData->user_id = ($request['user_id']);
        $userData->color = ($request['color']);
        $userData->save();

        return response()->json('please wait the manager to accept your account');
    }
}
