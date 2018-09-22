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
        $category_list = HotelMaster::select('hotel_category')->distinct()->get()->toArray();

        if($request->get('id') != Null && $request->get('id') != ''){
            return view('hotel_master.index',['id'=>$request->get('id'), 'category_list' => $category_list]);
        }else{
            return view('hotel_master.index', ['category_list' => $category_list]);
        }
    }

    public function getData(Request $request)
    {
        $columns = [];
        foreach (config('app.hotel_master_header_key') as $key => $value){
            array_push($columns,$key);
        }

        $hotelmaster = new HotelMaster();
        if($request->get('id') != Null && $request->get('id') != ''){
            $hotelmaster = $hotelmaster->where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')));
        }
        if($request->get('star') != Null && $request->get('star') != ''){
            $hotelmaster = $hotelmaster->where('hotel_stars',intval($request->get('star')));
        }
        if($request->get('rating') != Null && $request->get('rating') != ''){
            $start = \ceil($request->get('rating'));
            $end = $start + 1; 
            $hotelmaster = $hotelmaster->whereBetween(
                'booking_rating', array(
                    $start,
                    $end
                )
            );            
        }

        if($request->get('created_at_from') != Null && $request->get('created_at_from') != ''){
            $hotelmaster = $hotelmaster->where('created_at', '>=', Carbon::parse($request->get('created_at_from'))->startOfDay());
        }

        if($request->get('created_at_to') != Null && $request->get('created_at_to') != ''){
            $hotelmaster = $hotelmaster->where('created_at', '<=', Carbon::parse($request->get('created_at_to'))->endOfDay());
        }

        // if($request->get('created_at') != Null && $request->get('created_at') != ''){
        //     $start_date = Carbon::parse($request->get('created_at'))->startOfDay();
        //     $end_date = Carbon::parse($request->get('created_at'))->endOfDay();
        //     $hotelmaster = $hotelmaster->whereBetween(
        //         'created_at', array(
        //             $start_date,
        //             $end_date
        //         )
        //     );
        // }
        if($request->get('category') != Null && $request->get('category') != ''){
            $hotelmaster = $hotelmaster->where('hotel_category',$request->get('category'));
        }

        $totalData = $hotelmaster->count();
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $hotelmaster_data = $hotelmaster->offset(intval($start))
                     ->limit(intval($limit))
                     ->orderBy($order,$dir)
                     ->get();
        
        for($i=0; $i < count($hotelmaster_data); $i++)
        {
            //$hotelmaster_data[$i]['created_at'] = $hotelmaster_data[$i]['created_at'];
            $hotelmaster_data[$i]['hotel_id'] = '<a target="_blank" href="' . $hotelmaster_data[$i]['prop_url'] . '" title="View Property">' . $hotelmaster_data[$i]['hotel_id'] . '</a>';
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $hotelmaster_data   
                    );
            
        echo json_encode($json_data);
    }
}