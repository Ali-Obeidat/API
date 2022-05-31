<?php

namespace App\Http\Controllers\API;


use App\Models\BasicInformation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\profileInformaition;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Tarikhagustia\LaravelMt5\LaravelMt5;
use Illuminate\Support\Facades\Validator;

class apiBasicInformationController extends Controller
{
    // Function to Get a certain user basic information:
    public function getUserBasicInfo(Request $request)
    {
        $info = BasicInformation::where('user_id', $request['user_id'])->first();
        return response()->json(['user_info' => $info], 401);
    }
    // --------------------------------------------------------------------
    // Function to Store user basic information in database:
    public function store(Request $request)
    {
        $user_info = BasicInformation::where('user_id', $request['user_id'])->get();
        if (count($user_info) > 0) {
            return response()->json(['message' => 'you can\'t fill your information more than one time'], 401);
        }

        $validator = Validator::make($request->all(), [
            "full_name" => "required",
            'email' => "required| email|unique:basic_information",
            'Birth' => "required|date_format:Y-m-d|before_or_equal:2005-01-01",
            'Birth_location' => "required",
            'phone' => "required",
            'Citizenship' => "required",
            'city' => "required",
            'State' => "required",
            'Adders' => "required",
            'Zip_Code' => "required"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        BasicInformation::create([
            'user_id' => $request['user_id'],
            'full_name' => $request['full_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'Birth' => $request['Birth'],
            'citizenship' => $request['Citizenship'],
            'Birth_location' => $request['Birth_location'],
            'city' => $request['city'],
            'state' => $request['State'],
            'adders' => $request['Adders'],
            'zip_code' => $request['Zip_Code'],
        ]);

        // Change (basicinfo) filed value in user table to (1)
        $nowuser = User::find($request['user_id']);
        $nowuser->basicinfo = 1;
        $nowuser->save();
        // --------------------------------------------------

        // Send email to user that he filled his basic information:
        try {
            Mail::to($nowuser->email)->send(new profileInformaition());
        } catch (\Throwable $th) {
            //throw $th;
        }
        return response()->json(['message' => "basic info was created"]);

        // return redirect(route('demos.index'));
    }
    // ----------------------------------------------------------

    // function to update the name in basic information:
    public function update(Request $request,  $userId)
    {
        $validator = Validator::make($request->all(), [
            "f-name" => "required",
            "l-name" => "required",

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $info = BasicInformation::where('user_id', $userId)->first();
        $info->full_name = $request['f-name'] . " " . $request['l-name'];
        $info->save();
        return response()->json("name in basic info was updated");
    }

    public function changePassword(Request $request)
    {
        $user = User::find($request['user_id']);

        if (!(Hash::check($request->get('current_password'), $user->password))) {
            // The passwords matches
            return response()->json(["error" => "Your current password does not matches with the password."], 401);
        }

        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
            // Current password and new password same
            return response()->json(["error"=> "New Password cannot be same as your current password."],401);
        }

        $validatedData=  Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
   

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors());
        }
        //Change Password
        
        $user->password = Hash::make($request['new_password']);
        $user->save();

        return response()->json(["success"=> "Password successfully changed!"]);
    }
}
