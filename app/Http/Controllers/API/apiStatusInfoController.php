<?php

namespace App\Http\Controllers\API;
use App\Models\MtHulul;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tarikh\PhpMeta\Exceptions\ConnectionException;
use Tarikh\PhpMeta\Exceptions\UserException;
use Tarikhagustia\LaravelMt5\LaravelMt5;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;


class apiStatusInfoController extends Controller
{
    // Function to get state information
    public function getStateInfo(Request $request)
    {

        $api = new LaravelMt5();
        // get all auth user account
        $mtHulul = MtHulul::where('user_id',Auth::user()->id)->get();
        $account = MtHulul::find($request['login']);
        if ($request['login'] == "") {
            $login = "";
        }
        // return $id;
        $date = strtotime(date('F j+1, Y, g:i a'));
        $api = new LaravelMt5();
        $now = Carbon::now();
        $from = $now->startOfWeek()->format('Y-m-d H:i:s');
        $to = $now->endOfWeek()->format('Y-m-d H:i:s');


        $period = CarbonPeriod::create($from,  $to);
        // Convert the period to an array of dates
        $dates = $period->toArray();
       

        // Get Closed Order Total and pagination
        try {
            $total = $api->getOrderHistoryTotal($request['login'], 1641382555, $date);
            $chart = $api->getOrderHistoryTotal($request['login'], $now->startOfWeek()->timestamp, $now->endOfWeek()->timestamp);
        } catch (ConnectionException | UserException $e) {
            abort('connection Failed');
        }

        try {
            $trade = $api->getOrderHistoryPagination($request['login'], 1641382555, $date, 0, $total);
            $charts = $api->getOrderHistoryPagination($request['login'], $now->startOfWeek()->timestamp, $now->endOfWeek()->timestamp, 0, $chart);
            
            $balances = $api->getUserBatch($request['login']);
            $Balance = ['Balance_last_month'=>$balances[0]->BalancePrevMonth, 'Balance_yesterday' =>$balances[0]->BalancePrevDay, 'Balance_today'=> $balances[0]->Balance];
            $days = ['last month', 'yesterday', 'today'];

        
        } catch (ConnectionException | UserException $e) {
            abort('connection Failed');
        }
        return response() ->json( ['trades' => $trade, 'login' => $request['login'], 'date' => $date, 'mtHulul' => $mtHulul, 'days' => $days, 'charts' => $charts, 'Balance' => $Balance]);
        // return view('clientDashboard.filter', ['week'=>$weekday,'affiliate'=>$affiliate,'affiliateCount'=>$affiliateCount,'trades' => $trades, 'login' => $login, 'date' => $date, 'mtHulul' => $mtHulul, 'days' => $days, 'charts' => $charts, 'Balance' => $Balance]);
    }

    public function show($login)
    {

        $api = new LaravelMt5();
        // get all auth user account

        $mtHulul = MtHulul::where('user_id',Auth::user()->id)->get();
        $account = MtHulul::find($login);
        if ($login == "") {
            $login = "";
        }
        // return $id;
        $date = strtotime(date('F j+1, Y, g:i a'));

        $api = new LaravelMt5();

        // Iterate over the period

        $now = Carbon::now();
        $from = $now->startOfWeek()->format('Y-m-d H:i:s');
        $to = $now->endOfWeek()->format('Y-m-d H:i:s');
        // return $to;

        $period = CarbonPeriod::create($from,  $to);
        // Convert the period to an array of dates
        $dates = $period->toArray();
        // Get Closed Order Total and pagination
        try {
            $total = $api->getOrderHistoryTotal($login, 1641382555, $date);
            $chart = $api->getOrderHistoryTotal($login, $now->startOfWeek()->timestamp, $now->endOfWeek()->timestamp);
        } catch (ConnectionException | UserException $e) {
            abort('connection Failed');
        }
        try {
            $trade = $api->getOrderHistoryPagination($login, 1641382555, $date, 0, $total);
            $charts = $api->getOrderHistoryPagination($login, $now->startOfWeek()->timestamp, $now->endOfWeek()->timestamp, 0, $chart);
         
            $balances = $api->getUserBatch($login);
            // paginate the balances 
            $trades = $this->paginate($trade);
            // URL with paginate 
            $trades->withPath("/api/getStatePage/{$login}");
            $Balance = [$balances[0]->BalancePrevMonth, $balances[0]->BalancePrevDay, $balances[0]->Balance];
            $days = ['last month', 'yesterday', 'today'];
        } catch (ConnectionException | UserException $e) {
            abort('connection Failed');
        }
        return view('clientDashboard.filter', ['trades' => $trades, 'login' => $login, 'date' => $date, 'mtHulul' => $mtHulul, 'days' => $days,  'Balance' => $Balance]);
    }
    // Function to paginate the data 
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    // Function to filter the trade
    public function filter(Request $request)
    {
    //  return $request;
        if ($request['date'] == 'Today') {
            $from = Carbon::now()->startOfDay()->timestamp;
            $to = Carbon::tomorrow()->timestamp;
            // return $request;
        } elseif ($request['date'] == 'Yesterday') {
            $from = Carbon::yesterday()->timestamp;
            $to = Carbon::today()->timestamp;
        } elseif ($request['date'] == 'This Week') {
            $now = Carbon::now();
            $from = $now->startOfWeek()->timestamp;
            $to = $now->endOfWeek()->timestamp;
        } elseif ($request['date'] == 'Last Week') {
            $from = Carbon::now()->subWeek()->startOfWeek()->timestamp;
            $to = Carbon::now()->subWeek()->endOfWeek()->timestamp;
        } elseif ($request['date'] == 'This Month') {
            $from = Carbon::now()->startOfMonth()->timestamp;
            $to = Carbon::now()->endOfMonth()->timestamp;
        } elseif ($request['date'] == 'Last Month') {
            $from = Carbon::now()->subMonth()->startOfMonth()->timestamp;
            $to = Carbon::now()->subMonth()->endOfMonth()->timestamp;
        } else {
            $date = json_decode($request['daterange']);
            // return $date->start ;
            $from = strtotime($date->start);
            $to = strtotime($date->end);
        }

        // get all auth user account
        $mtHulul = MtHulul::where('user_id',Auth::user()->id)->get();

        $api = new LaravelMt5();

        try {
            $total = $api->getOrderHistoryTotal($request['login'], $from, $to);
        } catch (ConnectionException | UserException $e) {
        }
        try {
            $trade = $api->getOrderHistoryPagination($request['login'], $from, $to, 0, $total);
            //                 $x = $api->getPosition(101200,'EURUSD');
            // dd($x);
            // paginate the trade 
            $trades = $this->paginate($trade);
            // URL with paginate 
            $trades->withPath("/api/getStatePage/{$request['login']}");
            $balances = $api->getUserBatch($request['login']);
            $Balance = [$balances[0]->BalancePrevMonth, $balances[0]->BalancePrevDay, $balances[0]->Balance];
            $days = ['last month', 'yesterday', 'today'];
            // return $days;
        } catch (ConnectionException | UserException $e) {
        }
        return view('clientDashboard.filter', [ 'trades' => $trades, 'login' => $request['login'], 'mtHulul' => $mtHulul, 'days' => $days, 'Balance' => $Balance]);
    }
}
