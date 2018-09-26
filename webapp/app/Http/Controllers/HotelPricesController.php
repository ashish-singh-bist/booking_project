<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\HotelPrices;
use Carbon\Carbon;
use MongoDB\BSON\UTCDatetime;
use App\HotelMaster;

class HotelPricesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $room_type_list = HotelPrices::select('room_type')->distinct()->get()->toArray();
        $cancel_type_list = HotelPrices::select('cancellation_type')->distinct()->get()->toArray();
        $other_desc_list = HotelPrices::select('other_desc')->distinct()->get()->toArray();
        
        if($request->get('id') != Null && $request->get('id') != ''){
            return view('hotel_prices.index',['id'=>$request->get('id'), 'room_type_list' => $room_type_list, 'cancel_type_list'=>$cancel_type_list, 'other_desc_list'=>$other_desc_list]);
        }else{
            return view('hotel_prices.index', ['room_type_list' => $room_type_list, 'cancel_type_list'=>$cancel_type_list, 'other_desc_list'=>$other_desc_list]);
        }        
    }

    public function getData(Request $request)
    {
        $columns = [];
        foreach (config('app.hotel_prices_header_key') as $key => $value){
            array_push($columns,$key);
        }

        $hotelprices = new HotelPrices();
        $hotelmaster = HotelMaster::select('hotel_id');
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

        if(count($request->get('stars'))>0 || count($request->get('ratings'))>0 || count($request->get('countries'))>0 || count($request->get('cities'))>0){
            $hotel_id_data = $hotelmaster->get();
            $hotel_id_array = [];
            foreach($hotel_id_data as $value){
                array_push($hotel_id_array,$value->hotel_id);
            }
            $hotelprices = $hotelprices->whereIn('hotel_id',$hotel_id_array);
        }

        if($request->get('id') != Null && $request->get('id') != ''){
            $hotelprices = $hotelprices->where('hotel_id',$request->get('id'));
        } 

        if(count($request->get('room_types'))>0){
            $room_types = $request->get('room_types');
            $hotelprices = $hotelprices->where(function ($query) use ($room_types) {
                foreach($room_types as $key => $room_type){
                    if($key == 0){
                        $query = $query->where('room_type', $room_type);
                    }else{

                        $query = $query->orWhere('room_type', $room_type);
                    }
                }
                return $query;
            });
        }        

        if(count($request->get('max_persons'))>0){
            $max_persons = $request->get('max_persons');
            $hotelprices = $hotelprices->where(function ($query) use ($max_persons) {
                foreach($max_persons as $key => $max_person){
                    if($key == 0){
                        $query = $query->where('max_persons', intval($max_person));
                    }else{

                        $query = $query->orWhere('max_persons', intval($max_person));
                    }
                }
                return $query;
            });
        }           

        if($request->get('created_at_from') != Null && $request->get('created_at_from') != ''){
            $hotelprices = $hotelprices->where('created_at', '>=', Carbon::parse($request->get('created_at_from'))->startOfDay());
        }

        if($request->get('created_at_to') != Null && $request->get('created_at_to') != ''){
            $hotelprices = $hotelprices->where('created_at', '<=', Carbon::parse($request->get('created_at_to'))->endOfDay());
        }

        if($request->get('min_price') != Null && $request->get('min_price') != ''){
            $hotelprices = $hotelprices->whereBetween(
             'raw_price', array(
                 (int)$request->get('min_price'),
                 (int)$request->get('max_price')
             )
         );
        }

        if($request->get('checkin_date_from') != Null && $request->get('checkin_date_from') != ''){
            $hotelprices = $hotelprices->where('checkin_date', '>=', Carbon::parse($request->get('checkin_date_from'))->startOfDay());
        }

        if($request->get('checkin_date_to') != Null && $request->get('checkin_date_to') != ''){
            $hotelprices = $hotelprices->where('checkin_date', '<=', Carbon::parse($request->get('checkin_date_to'))->endOfDay());
        }
       
        if($request->get('meal_plan')!=Null && $request->get('meal_plan')!=''){
            $search_meal_plan = $request->get('meal_plan');
            if($search_meal_plan == 'empty'){
                $hotelprices = $hotelprices->whereNull('mealplan_included_name');
            } else if($search_meal_plan == 'not-empty'){
                $hotelprices = $hotelprices->whereNotNull('mealplan_included_name');
            }
        }
        if(count($request->get('cancellation_type'))>0){
            $cancel_type = $request->get('cancellation_type');
            $hotelprices = $hotelprices->where(function ($query) use ($cancel_type) {
                foreach($cancel_type as $key => $cancel_type){
                    if($key == 0){
                        $query = $query->where('cancellation_type', $cancel_type);
                    }else{

                        $query = $query->orWhere('cancellation_type', $cancel_type);
                    }
                }
                return $query;
            });
        }

        if(count($request->get('others_desc'))>0){
            $otherdesc = $request->get('others_desc');
            $hotelprices = $hotelprices->where(function ($query) use ($otherdesc) {
                foreach($otherdesc as $key => $otherdesc){
                    if($key == 0){
                        $query = $query->where('other_desc', $otherdesc);
                    }else{

                        $query = $query->orWhere('other_desc', $otherdesc);
                    }
                }
                return $query;
            });
        }

        if(count($request->get('days'))>0){
            $days = $request->get('days');
            $hotelprices = $hotelprices->where(function ($query) use ($days) {
                foreach($days as $key => $day){
                    if($key == 0){
                        $query = $query->where('number_of_days', intval($day));
                    }else{

                        $query = $query->orWhere('number_of_days', intval($day));
                    }
                }
                return $query;
            });
        }

        $statistics = [];
        $statistics['avg_price'] = $hotelprices->avg('raw_price') ?: 0;
        $statistics['max_price'] = $hotelprices->max('raw_price') ?: 0;
        $statistics['min_price'] = $hotelprices->min('raw_price') ?: 0;

        $totalData = $hotelprices->count();
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
            $hotelprices_data[$i]['raw_price'] = "&euro;" . $hotelprices_data[$i]['raw_price'];
            $hotelprices_data[$i]['checkin_date'] =  $hotelprices_data[$i]['checkin_date']->toDateTime()->format('Y M d');
        }
        
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $hotelprices_data,
                    "statistics" => $statistics
                    );
            
        echo json_encode($json_data);
    }
}