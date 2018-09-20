<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\HotelPrices;
use Carbon\Carbon;
use MongoDB\BSON\UTCDatetime;

class HotelPricesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $room_type_list = HotelPrices::select('room_type')->distinct()->get()->toArray();

        if($request->get('id') != Null && $request->get('id') != ''){
            return view('hotel_prices.index',['id'=>$request->get('id'), 'room_type_list' => $room_type_lis]);
        }else{
            return view('hotel_prices.index', ['room_type_list' => $room_type_list]);
        }        
    }

    public function getData(Request $request)
    {
        $columns = [];
        foreach (config('app.hotel_prices_header_key') as $key => $value){
            array_push($columns,$key);
        }

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
             'raw_price', array(
                 (int)$request->get('min_price'),
                 (int)$request->get('max_price')
             )
         );
        }
        if($request->get('checkin_date') != Null && $request->get('checkin_date') != ''){
            $start_date = Carbon::parse($request->get('checkin_date'))->startOfDay();
            $end_date = Carbon::parse($request->get('checkin_date'))->endOfDay();
            $hotelprices = $hotelprices->whereBetween(
             'checkin_date', array(
                 $start_date,
                 $end_date
             )
         );
        }            
        if($request->get('days') != Null && $request->get('days') != ''){
            $hotelprices = $hotelprices->where('number_of_days',(int)$request->get('days'));
        }

        #$avg_price = $hotelprices->avg('number_of_days');
        #print_r($avg_price);
        // $max_price = $hotelprices->max('price');
        // $min_price = $hotelprices->min('price');
        // $sum_price = $hotelprice->sum('price');
  
        $totalData = HotelPrices::count();
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        $hotelprices_data = $hotelprices->offset(intval($start))
                     ->limit(intval($limit))
                     ->orderBy($order,$dir)
                     ->get();

        for($i=0; $i < count($hotelprices_data); $i++)
        {
            //dd($hotelprices_data[$i]['created_at']);
            //$hotelprices_data[$i]['created_at'] = date('Y-m-d H:i:s',strtotime($hotelprices_data[$i]['created_at']));
            $hotelprices_data[$i]['raw_price'] = "$" . $hotelprices_data[$i]['raw_price'];
            $hotelprices_data[$i]['checkin_date'] =  $hotelprices_data[$i]['checkin_date']->toDateTime()->format('Y M d');
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $hotelprices_data   
                    );
            
        echo json_encode($json_data);
    }
}