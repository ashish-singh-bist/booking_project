<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use JsValidator;
use App\HotelPrices;

class HotelPricesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if($request->get('id') != Null && $request->get('id') != ''){
            return view('hotel_prices.index',['id'=>$request->get('id')]);
        }else{
            return view('hotel_prices.index');
        }        
    }

    public function getData(Request $request)
    {
        $hotelprices = new HotelPrices();
        if($request->get('id') != Null && $request->get('id') != ''){
            $hotelprices = $hotelprices->where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')));
        }        
        if($request->get('room_type') != Null && $request->get('room_type') != ''){
            $hotelprices = $hotelprices->where('room_type',$request->get('room_type'));
        }
        if($request->get('days') != Null && $request->get('days') != ''){
            $hotelprices = $hotelprices->where('number_of_days',(int)$request->get('days'));
        }
        if($request->get('guest') != Null && $request->get('guest') != ''){
            $hotelprices = $hotelprices->where('number_of_guests',(int)$request->get('guest'));
        }          
        $hotelprices_data = $hotelprices->get();
        return Datatables::of($hotelprices_data)->make(true);

    }
}