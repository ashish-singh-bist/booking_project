<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\User;
use App\CustomConfig;
use App\HotelMaster;
use App\StatsBooking;
use App\PropertyUrl;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::count();

        $p_total_count = PropertyUrl::count();
        $p_active_count = PropertyUrl::where('is_active',1)->count();

        //get parser stats
        $stats = StatsBooking::limit(10)->latest()->get();

        return view('home', [ 'user_count'=>$users, 'stats' => $stats, 'p_total_count' => $p_total_count, 'p_active_count' => $p_active_count]);
    }

    public function config()
    {
        $custom_config = CustomConfig::first();
        return view('config', [ 'custom_config'=> $custom_config]);
    }

    public function configUpdate(Request $request)
    {
        request()->validate([
            'parsing_interval' => 'required',
            'thread_count' => 'required',
            'number_of_guests' => 'required',
        ]);
        if( $request->has('scraper_active') )
            $scraper_active = 1;
        else
            $scraper_active = 0;
        $custom_config = CustomConfig::first();
        $custom_config->parsing_interval = intval($request->parsing_interval);
        $custom_config->thread_count = intval($request->thread_count);
        $custom_config->number_of_guests = intval($request->number_of_guests);
        $custom_config->scraper_active = intval($scraper_active);
        $custom_config->save();
        return view('config', [ 'custom_config'=> $custom_config]);
    }

    public function getFilterList(Request $request)
    {
        if($request->get('type') == 'Country'){
            $filter_list = HotelMaster::select('country')->where('country', 'like',$request->get('search') . '%')->distinct()->get()->toArray();
        }elseif($request->get('type') == 'City'){
            $filter_list = HotelMaster::select('city')->where('city', 'like',$request->get('search') . '%')->distinct()->get()->toArray();
        }

        $final_array = [];
        foreach($filter_list as $item) {
            $temp['id'] = $item[0];
            $temp['text'] = $item[0];
            array_push($final_array,$temp);
        }

        return response()->json($final_array);
    }

    public function restartParser(Request $request)
    {
        exec('python3 motn.py');
    }

}
