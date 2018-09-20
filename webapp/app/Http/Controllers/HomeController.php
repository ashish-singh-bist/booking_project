<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\CustomConfig;

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
        return view('home', [ 'user_count'=>$users]);
    }

    public function config()
    {
        $custom_config = CustomConfig::first();
        return view('config', [ 'custom_config'=> $custom_config->toArray()]);
    }

    public function configUpdate(Request $request)
    {
        $custom_config = CustomConfig::first();
        $custom_config->parsing_interval = $request->parsing_interval;
        $custom_config->thread_count = $request->thread_count;
        $custom_config->save();
        return view('config', [ 'custom_config'=> $custom_config->toArray()]);
    }  

}
