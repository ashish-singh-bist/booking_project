<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\HotelPrices;
use Carbon\Carbon;

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
        if($request->get('max_persons') != Null && $request->get('max_persons') != ''){
            $hotelprices = $hotelprices->where('max_persons',(int)$request->get('max_persons'));
        }        
        if($request->get('created_at') != Null && $request->get('created_at') != ''){
            $start_date = Carbon::parse($request->get('created_at'))->startOfDay();
            $end_date = Carbon::parse($request->get('created_at'))->endOfDay();
            $hotelprices = $hotelprices->whereBetween(
             'created_at', array(
                 $start_date,
                 $end_date
             )
         );
        }
        if($request->get('min_price') != Null && $request->get('min_price') != ''){
            $hotelprices = $hotelprices->whereBetween(
             'room_price', array(
                 $request->get('min_price'),
                 $request->get('max_price')
             )
         );
        }        
        if($request->get('days') != Null && $request->get('days') != ''){
            $hotelprices = $hotelprices->where('number_of_days',(int)$request->get('days'));
        }        
        $hotelprices_data = $hotelprices->limit(1000)->get();
        return Datatables::of($hotelprices_data)->make(true);

    }
}