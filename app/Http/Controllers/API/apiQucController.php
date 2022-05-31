<?php

namespace App\Http\Controllers\API;

use App\Models\Quc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\FinancialProfile;
use RealRashid\SweetAlert\Facades\Alert;

class apiQucController extends Controller
{
    // Function to get Financial profile answers
    public function getUserQuc(Request $request)
    {
        $userQuc = Quc::where('user_id', $request['user_id'])->get();
        return response()->json(['Financial_info'=>$userQuc]) ;
    }
    // ------------------------------------------

    // Function to store Financial profile answers in database
    public function storeQuc(Request $request)
    {

        $user_Quc= Quc::where('user_id',$request['user_id'])->get();
        if (count($user_Quc) > 0) {
            return response()->json('you can\'t fill your Financial profile info more than one time'); 
        }
        // check if status is Student or not
        
            // create record in database
            Quc::create([
                'user_id' => $request['user_id'],
                'married' => $request['married'],
                'Income&Investments' => $request['Income&Investments'],
    
                'Available_Amount' => $request['Available_Amount'],
                'Status' => $request['Status'],
                'agree' => 1,
            ]);
            $user = User::find($request['user_id']);
            $user->qyc = 1;
            $user->save();
            // clear the session
           
            // send email to user that Financial profile was created
            try {
                Mail::to($user->email)->send(new FinancialProfile());
            } catch (\Throwable $th) {
            }

            return response()->json('Financial profile was created') ;
       

            // change (qyc) filed in user table to 1
            $user = User::find($request['user_id']);
            $user->qyc = 1;
            $user->save();

            // send email to user that Financial profile was created
          
            // clear the session
       
            return response()->json('Financial profile was created') ;
        
    }
}
