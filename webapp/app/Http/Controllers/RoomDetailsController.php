<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\RoomDetails;
use Carbon\Carbon;

class RoomDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {   
        if($request->get('id') != Null && $request->get('id') != ''){
            return view('room_details.index',['id'=>$request->get('id')]);
        }else{
            return view('room_details.index');
        }          
    }

    public function getData(Request $request)
    {
        $roomdetails = new RoomDetails();
        if($request->get('id') != Null && $request->get('id') != ''){
            $roomdetails = $roomdetails->where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')));
        }         
        if($request->get('room_type') != Null && $request->get('room_type') != ''){
            $roomdetails = $roomdetails->where('room_type',$request->get('room_type'));
        }
        if($request->get('created_at') != Null && $request->get('created_at') != ''){
            $start_date = Carbon::parse($request->get('created_at'))->startOfDay();
            $end_date = Carbon::parse($request->get('created_at'))->endOfDay();
            $roomdetails = $roomdetails->whereBetween(
             'created_at', array(
                 $start_date,
                 $end_date
             )
         );
        }
        $roomdetails_data = $roomdetails->limit(1000)->get();
        return Datatables::of($roomdetails_data)->make(true);        
    }
}