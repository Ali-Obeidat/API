<?php

namespace App\Http\Controllers\API;

use App\Events\SendMail;
use App\Mail\AgreeddEmail;
use App\Models\UserAccounts;
use App\Models\MtHulul;
use Illuminate\Http\Request;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tarikhagustia\LaravelMt5\LaravelMt5;
use Tarikh\PhpMeta\MetaTraderClient;
use RealRashid\SweetAlert\Facades\Alert;
use Tarikh\PhpMeta\Lib\MTUserProtocol;
use Tarikhagustia\LaravelMt5\Entities\Trade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;




class apiUserAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // Function to get all demo accounts
    public function getDemoAccounts(Request $request)
    {
        $accountInformations = ['balance' => null, 'equity' => null, 'freeMargin' => null];
        $accountsInformations = [];

        // get all auth user accounts from mt_hulul table
        $userAccounts = MtHulul::Where('user_id', $request['user_id'])->where('group', 'demo\demoTest')->get();

        $api = new LaravelMt5();
        foreach ($userAccounts as $demo) {
            // check if account group is demo\demoTest
            $user = $api->getTradingAccounts($demo->login);

            $accountInformations['balance'] = $user->Balance;
            $accountInformations['equity'] = $user->Equity;
            $accountInformations['freeMargin'] = $user->MarginFree;
            $accountInformations['login'] = $user->Login;
            $accountInformations['name'] = $demo->name;
            $accountInformations['id'] = $demo->id;
            $accountInformations['leverage'] = $demo->leverage;
            $accountInformations['group'] = $demo->group;
            $accountInformations['created_at'] = date('d/m/Y', strtotime($demo->created_at));

            array_push($accountsInformations, $accountInformations);
        }
        return response()
            ->json(['demo_accounts_Informations' => $accountsInformations]);
    }
    // Function to get all real accounts
    public function showTrading(Request $request)
    {
        $accountInformations = ['balance' => null, 'equity' => null, 'freeMargin' => null];
        $accountsInformations = [];
        // get all real accounts from mt_hulul table
        $demos = MtHulul::all()->Where('user_id', $request['user_id'])->where('group', 'preliminary');
        $api = new LaravelMt5();
        foreach ($demos as $demo) {
            // check if account group is preliminary
            $user = $api->getTradingAccounts($demo->login);
            $accountInformations['balance'] = $user->Balance;
            $accountInformations['equity'] = $user->Equity;
            $accountInformations['freeMargin'] = $user->MarginFree;
            $accountInformations['login'] = $user->Login;
            $accountInformations['name'] = $demo->name;
            $accountInformations['id'] = $demo->id;
            $accountInformations['leverage'] = $demo->leverage;
            $accountInformations['group'] = $demo->group;
            $accountInformations['created_at'] = date('d/m/Y', strtotime($demo->created_at));

            array_push($accountsInformations, $accountInformations);
        }


        return response()
            ->json(['real_accounts_Informations' => $accountsInformations]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserAccounts  $userAccounts
     * @return \Illuminate\Http\Response
     */
    public function show(UserAccounts $userAccounts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserAccounts  $userAccounts
     * @return \Illuminate\Http\Response
     */
    public function edit(UserAccounts $userAccounts)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserAccounts  $userAccounts
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserAccounts $userAccounts)
    {
        //
    }
}
