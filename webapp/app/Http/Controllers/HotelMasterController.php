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
            $hotelmaster = $hotelmaster->where('hotel_id',$request->get('id'));
        }
        if(count($request->get('stars'))>0){
            $stars = $request->get('stars');
            $hotelmaster = $hotelmaster->where(function ($query) use ($stars) {
                foreach($stars as $key => $star){
                    if($key == 0){
                        $query = $query->where('hotel_stars', $star);
                    }else{

                        $query = $query->orWhere('hotel_stars', $star);
                    }
                }
                return $query;
            });
        }

        if(count($request->get('ratings'))>0){
            $ratings = $request->get('ratings');
            $hotelmaster = $hotelmaster->where(function ($query) use ($ratings) {
                foreach($ratings as $key => $rating){
                    $start = intval($rating);
                    $end = $rating + 1;                     
                    if($key == 0){
                        $query = $query->where(function ($query_inner) use ($ratings,$start,$end) {
                            return $query_inner->where('booking_rating', '>=', $start)->Where('booking_rating', '<', $end);
                        });
                    }else{
                        $query = $query->orWhere(function ($query_inner) use ($ratings,$start,$end) {
                            return $query_inner->where('booking_rating', '>=', $start)->Where('booking_rating', '<', $end);
                        });
                    }
                }
                return $query;
            });
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

        if(count($request->get('countries'))>0){
            $countries = $request->get('countries');
            $hotelmaster = $hotelmaster->where(function ($query) use ($countries) {
                foreach($countries as $key => $country){
                    if($key == 0){
                        $query = $query->where('country', $country);
                    }else{

                        $query = $query->orWhere('country', $country);
                    }
                }
                return $query;
            });
        }

        if(count($request->get('cities'))>0){
            $cities = $request->get('cities');
            $hotelmaster = $hotelmaster->where(function ($query) use ($cities) {
                foreach($cities as $key => $city){
                    if($key == 0){
                        $query = $query->where('city', $city);
                    }else{

                        $query = $query->orWhere('city', $city);
                    }
                }
                return $query;
            });
        }        

        if(count($request->get('categories'))>0){
            $categories = $request->get('categories');
            $hotelmaster = $hotelmaster->where(function ($query) use ($categories) {
                foreach($categories as $key => $category){
                    if($key == 0){
                        $query = $query->where('hotel_category', $category);
                    }else{

                        $query = $query->orWhere('hotel_category', $category);
                    }
                }
                return $query;
            });
        }

        $statistics = [];
        $statistics['avg_rating'] = $hotelmaster->avg('booking_rating') ?: 0;
        $statistics['max_rating'] = $hotelmaster->max('booking_rating') ?: 0;
        $statistics['min_rating'] = $hotelmaster->min('booking_rating') ?: 0;

        // $hotelmaster = $hotelmaster->where('lng_lat', 'near', [
        //     '$geometry' => ['type' => 'Point', 'coordinates' => [51.33631274, 12.39401847]],
        //     '$maxDistance' => 200000,
        // ]);
        // print_r($hotelmaster);
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
                    "data"            => $hotelmaster_data,
                    "statistics" => $statistics
                    );
            
        echo json_encode($json_data);
    }
}