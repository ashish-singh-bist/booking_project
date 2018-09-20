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
    {   $room_type_list = RoomDetails::select('room_type')->distinct()->get()->toArray();

        if($request->get('id') != Null && $request->get('id') != ''){
            return view('room_details.index',['id'=>$request->get('id'), 'room_type_list' => $room_type_list]);
        }else{
            return view('room_details.index', ['room_type_list' => $room_type_list]);
        }          
    }

    public function getData(Request $request)
    {
        $columns = [];
        foreach (config('app.room_details_header_key') as $key => $value){
            array_push($columns,$key);
        }

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
        if($request->get('checkin_date') != Null && $request->get('checkin_date') != ''){
            $start_date = Carbon::parse($request->get('checkin_date'))->startOfDay();
            $end_date = Carbon::parse($request->get('checkin_date'))->endOfDay();
            $roomdetails = $roomdetails->whereBetween(
             'checkin_date', array(
                 $start_date,
                 $end_date
             )
         );
        }        
        $totalData = $roomdetails->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $roomdetails_data = $roomdetails->offset(intval($start))
                     ->limit(intval($limit))
                     ->orderBy($order,$dir)
                     ->get();

        for($i=0; $i < count($roomdetails_data); $i++)
        {
            //$roomdetails_data[$i]['created_at'] = $roomdetails_data[$i]['created_at'];
            $roomdetails_data[$i]['checkin_date'] =  $roomdetails_data[$i]['checkin_date']->toDateTime()->format('Y M d');
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $roomdetails_data   
                    );
            
        echo json_encode($json_data);         

        // $roomdetails_data = Datatables::of($roomdetails_data)
        //     ->editColumn('created_at', function(RoomDetails $roomdetails_data) {
        //         return $roomdetails_data->created_at->format('d M Y');
        //     })
        //     ->with([
        //         'recordsTotal' => intval($totalData),
        //         'recordsFiltered' => intval($totalFiltered),
        //     ])
        //     ->make(true);

        // return $roomdetails_data;
    }
}