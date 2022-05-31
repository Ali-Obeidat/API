<?php

namespace App\Http\Controllers\API;

use App\Models\User as LoginUser;

use App\Mail\ChangeLeverage;
use App\Models\BasicInformation;
use App\Models\MtHulul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;
use Tarikh\PhpMeta\Exceptions\ConnectionException;
use Tarikh\PhpMeta\Exceptions\UserException;
use Tarikh\PhpMeta\Entities\User;
use Tarikhagustia\LaravelMt5\LaravelMt5;
use App\Mail\welcomeEmail;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Hash;
use Tarikh\PhpMeta\MetaTraderClient;
use Tarikh\PhpMeta\Lib\MTUserProtocol;
use Tarikhagustia\LaravelMt5\Entities\Trade;
use Illuminate\Support\Facades\Validator;


class apiDemoController extends Controller
{
    // Function to create demo account:
    public function store(Request $request)
    {
        $api = new LaravelMt5();

       

        // Check in the user has filled his basic information or not
        $userlog = LoginUser::find($request['user_id']);
        $info = BasicInformation::where('user_id', $userlog->id)->get();
        if (empty($info[0])) {
            
            // create new user in meta 
            $user = new User();
            // if not we set name, email, password like user name, email, password
            $user->setName($userlog->name);
            $user->setEmail($userlog->email);
            $user->setGroup('demo\demoTest');
            $user->setLeverage(($request->leverage));

            $user->setMainPassword(Hash::make($request->password));
            $user->setInvestorPassword(Hash::make($request->password));

            $result = $api->createUser($user);

            // Create new record in MT_hulul table:
            $userData = new MtHulul();
            $userData->name = $userlog->name;
            $userData->login = $result->getLogin();
            $userData->email = $userlog->email;
            $userData->group = ('demo\demoTest');
            $userData->leverage = (($request->leverage));
            $userData->account_type = 'Demo';
            $userData->currency = (($request->currency));

            $userData->password = (Hash::make($request->password));
            $userData->invest_password = (Hash::make($request->password));
            $userData->phone_password = (Hash::make($request->password));
            $userData->user_id = ($request['user_id']);
            $userData->color = ($request['color']);
            $userData->save();
        } else {
            // if the user has filled his basic information 
            // create new user in meta 
            $user = new User();
            $user->setName($userlog->name);
            $user->setEmail($userlog->email);
            $user->setGroup('demo\demoTest');
            $user->setLeverage(($request->leverage));
            $user->setPhone($info[0]->phone);
            $user->setAddress($info[0]->adders);
            $user->setCity($info[0]->city);
            $user->setState($info[0]->state);
            $user->setCountry($info[0]->citizenship);
            $user->setZipCode($info[0]->zip_code);
            $user->setMainPassword(Hash::make($request->password));
            $user->setInvestorPassword(Hash::make($request->password));
            $user->setPhonePassword(Hash::make($request->password));
            $result = $api->createUser($user);

            // Create new record in MT_hulul table:
            $userData = new MtHulul();
            $userData->name = $userlog->name;
            $userData->login = $result->getLogin();
            $userData->email = $userlog->email;
            $userData->group = ('demo\demoTest');
            $userData->leverage = (($request->leverage));
            $userData->account_type = 'Demo';
            $userData->currency = (($request->currency));
            $userData->phone = ($info[0]->phone);
            $userData->address = ($info[0]->adders);
            $userData->city = ($info[0]->city);
            $userData->state = ($info[0]->state);
            $userData->country = ($info[0]->citizenship);
            $userData->zipcode = ($info[0]->zip_code);
            $userData->password = (Hash::make($request->password));
            $userData->invest_password = (Hash::make($request->password));
            $userData->phone_password = (Hash::make($request->password));
            $userData->user_id = ($request['user_id']);
            $userData->color = ($request['color']);
            $userData->save();
        }
        // return $info[0] ;
        // $api = new MetaTraderClient('198.244.148.208', '443', '1005', 'ABCD1234');
        try {
            $balance = new  MTUserProtocol($api);
            $api->conductUserBalance($result->getLogin(), Trade::DEAL_BALANCE, (int)($request->Balance), 'aaaaaa');
        } catch (\Throwable $th) {
            //throw $th;
        }

        //Send email to user that he created new demo account:
        Mail::to($userlog->email)->send(new welcomeEmail($userData));
        try {
        } catch (\Throwable $th) {
            //throw $th;
        }
        return response([
            'message' => 'Demo Live trade account was created'
        ]);
    }
    // Function to delete a certain account 
    public function destroy($login)
    {
        // get the account from mt_hulul table by login
        $demos = MtHulul::Where('login', $login)->get();
        // --------------------------------------------
        try {
            // delete the account from database
            $demos[0]->delete();
            $api = new LaravelMt5();
            try {
                // delete the account from meta
                $all = $api->deleteUser($login);
            } catch (ConnectionException | UserException $e) {
            }
            return ['the account with login: ' . $login . ' was deleted'];
        } catch (\Throwable $th) {
            return ['the delete was failed'];
        }
    }

    // Function to change account leverage:
    public function leverageUpdate(Request $request, $login)
    {
        // find the user by the id 
        // return $login;
        $userlog = LoginUser::find($request['user_id']);
        // --------------------------

        // find the account by the id 
        $login = MtHulul::where('login', $login)->first();
        // ------------------------------
        // change account leverage in meta
        $api = new LaravelMt5();
        $api2 = new  MetaTraderClient('198.244.148.208', '443', '1005', 'abcd1234');
        $user = new User();
        $user->Login = $login->login;
        $user->Email = $userlog->email;
        $user->Group = 'demo\demoTest';
        $user->Leverage = $request->account_type;
        $user->Name = $userlog->name;
        $user->Company = null;
        $user->Language = null;
        $user->Country = null;
        $user->City = null;
        $user->State = null;
        $user->ZipCode = null;
        $user->Address = null;
        $user->ID = null;
        $user->Phone = null;
        $user->Status = null;
        $user->Comment = null;
        $user->Color = null;
        $user->PhonePassword = null;
        $user->Agent = null;
        $user->Rights = null;
        $user->MainPassword = ($userlog->password);
        $user->InvestorPassword = ($userlog->password);
        $api2->updateUser($user);

        // change account leverage in database (mt_hulul table)
        $login->update(['leverage' => $request['leverage']]);

        // Send email to user that he change account leverage:


        try {
            Mail::to($userlog->email)->send(new ChangeLeverage($user));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return response()->json('leverage was changed to ' . $request['leverage']);
    }

    // Function to change account balance:
    public function changeBalance($login, Request $request)
    {
        $api = new MetaTraderClient('198.244.148.208', '443', '1005', 'kopiuy21sa');
        $balance = new  MTUserProtocol($api);
        $api->conductUserBalance($login, Trade::DEAL_BALANCE, (int)($request->Balance), 'aaaaaa');
        // $x= $api->getUserBalance($login);
        // dd($x);
        
        return ['Change Balance' => 'Balance was changed'];
    }
}
