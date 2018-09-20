<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\RoomsAvailability;

class RoomsAvailabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $room_type_list = RoomsAvailability::select('room_type')->distinct()->get()->toArray();

        if($request->get('id') != Null && $request->get('id') != ''){
            return view('rooms_availability.index',['id'=>$request->get('id'), 'room_type_list' => $room_type_list]);
        }else{
            return view('rooms_availability.index', ['room_type_list' => $room_type_list]);
        }         
    }

    public function getData(Request $request)
    {
        $columns = [];
        foreach (config('app.rooms_availability_header_key') as $key => $value){
            array_push($columns,$key);
        }

        $roomsavailability = new RoomsAvailability();
        if($request->get('id') != Null && $request->get('id') != ''){
            $roomsavailability = $roomsavailability->where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')));
        }        
        if($request->get('room_type') != Null && $request->get('room_type') != ''){
            $roomsavailability = $roomsavailability->where('room_type',$request->get('room_type'));
        }
        if($request->get('days') != Null && $request->get('days') != ''){
            $roomsavailability = $roomsavailability->where('number_of_days',(int)$request->get('days'));
        }        
        if($request->get('available_only') != Null && $request->get('available_only') != ''){
            $roomsavailability = $roomsavailability->where('available_only',(int)$request->get('available_only'));
        }
        if($request->get('created_at') != Null && $request->get('created_at') != ''){
            $start_date = Carbon::parse($request->get('created_at'))->startOfDay();
            $end_date = Carbon::parse($request->get('created_at'))->endOfDay();
            $roomsavailability = $roomsavailability->whereBetween(
             'created_at', array(
                 $start_date,
                 $end_date
             )
         );
        }
        if($request->get('checkin_date') != Null && $request->get('checkin_date') != ''){
            $start_date = Carbon::parse($request->get('checkin_date'))->startOfDay();
            $end_date = Carbon::parse($request->get('checkin_date'))->endOfDay();
            $roomsavailability = $roomsavailability->whereBetween(
             'checkin_date', array(
                 $start_date,
                 $end_date
             )
         );
        } 
        
        $totalData = $roomsavailability->count();
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        $roomsavailability_data = $roomsavailability->offset((int)$start)
                     ->limit((int)$limit)
                     ->orderBy($order,$dir)
                     ->get();

        for($i=0; $i < count($roomsavailability_data); $i++)
        {
            //$roomsavailability_data[$i]['created_at'] = $roomsavailability_data[$i]['created_at'];
            $roomsavailability_data[$i]['checkin_date'] =  $roomsavailability_data[$i]['checkin_date']->toDateTime()->format('Y M d');
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $roomsavailability_data   
                    );
            
        echo json_encode($json_data);
    }
}