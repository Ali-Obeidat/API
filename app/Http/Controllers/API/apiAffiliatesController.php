<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\API\Controller;


use App\Models\User;
use Illuminate\Http\Request;

class apiAffiliatesController extends Controller
{
    //Function to Get all registered users via a certain user referral link:
    public function getAllAffiliates (Request $request)
    {
        $referrals = User::where('referred_by', $request['user_id'])->get();
        return response() ->json(['Affiliates_users'=>$referrals]);
        
    }
    // -------------------------------------------------------------------------- 

    // Function to create user referral link:
    public function getReferralLink (Request $request)
    {
        return route('showRegistrationForm', ['ref' => $request['user_id']]);
    }
}
