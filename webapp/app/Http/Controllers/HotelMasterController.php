<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\HotelMaster;
use Carbon\Carbon;

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
        $hotelmaster = new HotelMaster();
        if($request->get('id') != Null && $request->get('id') != ''){
            $hotelmaster = $hotelmaster->where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')));
        }        
        if($request->get('star') != Null && $request->get('star') != ''){
            $hotelmaster = $hotelmaster->where('hotel_stars',$request->get('star'));
        }
        if($request->get('rating') != Null && $request->get('rating') != ''){
            $hotelmaster = $hotelmaster->where('booking_rating',$request->get('rating'));
        }
        if($request->get('created_at') != Null && $request->get('created_at') != ''){
            $start_date = Carbon::parse($request->get('created_at'))->startOfDay();
            $end_date = Carbon::parse($request->get('created_at'))->endOfDay();
            $hotelmaster = $hotelmaster->whereBetween(
             'created_at', array(
                 $start_date,
                 $end_date
             )
         );
        }
        if($request->get('category') != Null && $request->get('category') != ''){
            $hotelmaster = $hotelmaster->where('hotel_category',$request->get('category'));
        }        
        $hotelmaster_data = $hotelmaster->limit(1000)->get();
        return Datatables::of($hotelmaster_data)->make(true); 
    }
}