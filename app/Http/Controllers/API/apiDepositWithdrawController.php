<?php

namespace App\Http\Controllers\API;

use App\Models\UserAccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class apiDepositWithdrawController extends Controller
{
    // Function to create Deposits and withdrawals
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
            'user_id' => ['required'],
            'Amount' => ['required'],
            'user_login' => ['required'],
            'type' => ['required'],
            'date' => ['required'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());       
        }
        
        UserAccounts::create([
            'user_id' =>$request['user_id'] ,
            'Amount' => $request['Amount'],
            'user_login' => $request['user_login'],
            'type' => $request['type'],
            'date' => $request['date'],
        ]);
        return response()->json(['your request to ' . $request['type'] .'in account with login: '. $request['user_login']]);
    }
}
