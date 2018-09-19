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
        if($request->get('id') != Null && $request->get('id') != ''){
            return view('rooms_availability.index',['id'=>$request->get('id')]);
        }else{
            return view('rooms_availability.index');
        }         
    }

    public function getCustomFilter()
    {
        return view('datatables.collection.custom-filter');
    }

    public function getData(Request $request)
    {
        $roomsavailability = new RoomsAvailability();
        if($request->get('id') != Null && $request->get('id') != ''){
            $roomsavailability = $roomsavailability->where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')));
        }        
        if($request->get('room_type') != Null && $request->get('room_type') != ''){
            $roomsavailability = $roomsavailability->where('room_type',$request->get('room_type'));
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
        $roomsavailability_data = $roomsavailability->limit(1000)->get();
        return Datatables::of($roomsavailability_data)->make(true);
        // return Datatables::of($roomsavailability)
        //     ->filter(function ($instance) use ($request) {
        //         if ($request->has('room_type') && $request->get('room_type')) {
        //             $instance->collection = $instance->collection->filter(function ($row) use ($request) {
        //                 return Str::contains($row['room_type'], $request->get('room_type')) ? true : false;
        //             });
        //         }
        //     })
        //     ->make(true);
        //return Datatables::of($roomsavailability)->make(true);
    }
}