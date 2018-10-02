<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\HotelPrices;
use Carbon\Carbon;
use MongoDB\BSON\UTCDatetime;
use App\HotelMaster;
use DB;

class ChartPricesController extends Controller
{
    public function index(Request $request)
    {
    	$date_array = [];
        $price_array = [];
        $room_type_list = HotelPrices::select('room_type')->distinct()->get()->toArray();
        $cancel_type_list = HotelPrices::select('cancellation_type')->distinct()->get()->toArray();
        $other_desc_list = HotelPrices::select('other_desc')->distinct()->get()->toArray();
        $hotel_type = HotelMaster::select('hotel_name')->distinct()->get()->toarray();
        if($request->get('id') != Null && $request->get('id') != ''){
            return view('hotel_prices/chart_prices',['id'=>$request->get('id'), 'room_type_list' => $room_type_list, 'cancel_type_list'=>$cancel_type_list, 'other_desc_list'=>$other_desc_list, 'hotel_type'=>$hotel_type, 'date_array'=> json_encode($date_array), 'price_array'=>json_encode($price_array)]);
        }else{
            return view('hotel_prices/chart_prices', ['room_type_list' => $room_type_list, 'cancel_type_list'=>$cancel_type_list, 'other_desc_list'=>$other_desc_list, 'hotel_type'=>$hotel_type, 'date_array'=>json_encode($date_array), 'price_array'=>json_encode($price_array)]);
        }
    }

    public function getChartData(Request $request)
    {
        $columns = [];
        foreach (config('app.hotel_prices_header_key') as $key => $value){
            array_push($columns,$key);
        }

        $date_array = [];
        $price_array = [];

        $hotelprices = HotelPrices::select('_id','checkin_date','raw_price');
        $hotelmaster = HotelMaster::select('hotel_id');
        
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

        if(count($request->get('cities'))>0){
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

        if($request->get('created_at') != Null && $request->get('created_at') != ''){
            $hotelprices = $hotelprices->where('created_at', '>=', Carbon::parse($request->get('created_at'))->startOfDay());
            $hotelprices = $hotelprices->where('created_at', '<=', Carbon::parse($request->get('created_at'))->endOfDay());
        }

        if($request->get('checkin_date_from') != Null && $request->get('checkin_date_from') != ''){
            $hotelprices = $hotelprices->where('checkin_date', '>=', Carbon::parse($request->get('checkin_date_from'))->startOfDay());
        }

        if($request->get('checkin_date_to') != Null && $request->get('checkin_date_to') != ''){
            $hotelprices = $hotelprices->where('checkin_date', '<=', Carbon::parse($request->get('checkin_date_to'))->endOfDay());
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

        $hotelprices_data = $hotelprices->select('*')->orderBy('checkin_date','ASC')->get();

        $chart_data_array = ['checkin_date' => []];
        if(count($hotelprices_data)){
            $c_date = Carbon::parse($hotelprices_data[0]['checkin_date']->toDateTime()->format('y-m-d'));
            $end_date = $hotelprices_data[(count($hotelprices_data)-1)]['checkin_date']->toDateTime();

            for($start_date = $c_date; $start_date<=$end_date; $start_date = Carbon::parse($start_date)->addDay()){
                array_push($chart_data_array['checkin_date'],$start_date->format('y-m-d'));
            }
            
            for($i=0; $i < count($hotelprices_data); $i++){
                $unique_key = $hotelprices_data[$i]['room_type'] . '|' . $hotelprices_data[$i]['number_of_days'] . '|' . $hotelprices_data[$i]['nr_stays'] . '|' . $hotelprices_data[$i]['max_persons'] . '|' . $hotelprices_data[$i]['cancellation_type'] . '|' . $hotelprices_data[$i]['mealplan_included_name'];
                $check_in_date = $hotelprices_data[$i]['checkin_date']->toDateTime()->format('y-m-d');
                if($c_date->format('y-m-d') == $check_in_date){

                    $index = array_search($check_in_date, $chart_data_array['checkin_date']);
                    
                    if (array_key_exists($unique_key,$chart_data_array)){
                        //array_push($chart_data_array[$unique_key],$hotelprices_data[$i]['raw_price']);
                        $chart_data_array[$unique_key][$index] = $hotelprices_data[$i]['raw_price'];
                    }else
                    {
                        $chart_data_array[$unique_key] = [];
                        for($j=0; $j<$index; $j++){
                            array_push($chart_data_array[$unique_key],null);
                        }
                        $chart_data_array[$unique_key][$index] = $hotelprices_data[$i]['raw_price'];
                    }

                }else{
                    if($i+1 <= count($hotelprices_data) - 1){
                        $next_check_in_date = $hotelprices_data[$i+1]['checkin_date']->toDateTime();

                        for($start_date = $c_date; $start_date < $next_check_in_date; $start_date = Carbon::parse($start_date)->addDay()){
                            foreach($chart_data_array as $key => $value){
                                if($key != 'checkin_date'){
                                    array_push($chart_data_array[$key],null);
                                }
                            }
                        }
                        $c_date = Carbon::parse($c_date)->addDay();
                    }
                }
            }
        }

        $json_data = array(
                    "data"            => [],
                    "chart_data_array" 	  => $chart_data_array,
                    );
            
        echo json_encode($json_data);
    }
}