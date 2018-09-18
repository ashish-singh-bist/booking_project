<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use JsValidator;
use App\HotelMaster;

class HotelMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {   
        if($request->get('id') != Null && $request->get('id') != ''){
            return view('hotel_master.index',['id'=>$request->get('id')]);
        }else{
            return view('hotel_master.index');
        }
    }

    public function getData(Request $request)
    {  
        if($request->get('id') != Null && $request->get('id') != ''){
            $hotelmaster = HotelMaster::where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')))->get();
        }else{
            $hotelmaster = HotelMaster::get();
        }
        return Datatables::of($hotelmaster)->make(true);
    }
}