<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\HotelPrices;
use Carbon\Carbon;
use MongoDB\BSON\UTCDatetime;
use App\HotelMaster;
use App\RoomDetails;
use Response;
use DB;
use App\PropertyUrl;
use App\DistinctData;

class HotelPricesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $room_type_list       = DistinctData::select('room_type')->get()->toArray();
        $cancel_type_list     = DistinctData::select('cancellation_type')->get()->toArray();
        $other_desc_list      = DistinctData::select('other_desc')->get()->toarray();
        $category_list        = DistinctData::select('hotel_category')->get()->toArray();
        $max_person_list      = DistinctData::select('max_persons')->get()->toArray();
        $available_room_list  = DistinctData::select('available_only')->get()->toArray();

        if($request->get('id') != Null && $request->get('id') != ''){
            return view('hotel_prices.index',['id'=>$request->get('id'), 'cancel_type_list'=> $cancel_type_list[0]['cancellation_type'], 'other_desc_list'=> $other_desc_list[0]['other_desc'], 'category_list'=> $category_list[0]['hotel_category'], 'room_type_list'=> $room_type_list[0]['room_type'], 'max_person_list'=> $max_person_list[0]['max_persons'], 'available_room_list'=> $available_room_list[0]['available_only']]);
        }else{
            return view('hotel_prices.index', ['cancel_type_list'=> $cancel_type_list[0]['cancellation_type'], 'other_desc_list'=> $other_desc_list[0]['other_desc'], 'category_list'=> $category_list[0]['hotel_category'], 'room_type_list'=> $room_type_list[0]['room_type'], 'max_person_list'=> $max_person_list[0]['max_persons'], 'available_room_list'=> $available_room_list[0]['available_only']]);
        }        
    }

    public function getData(Request $request)
    {
        $columns = [];
        $columns_header = [];
        foreach (config('app.hotel_prices_header_key') as $key => $value){
            array_push($columns,$key);
            array_push($columns_header,$value);
        }

        $hotel_name_list = HotelMaster::select('hotel_id', 'hotel_name', 'hotel_category', 'hotel_stars', 'location', 'booking_rating','guests_favorite_area', 'self_verified', DB::raw('SUM(total) as total'))->groupBy('hotel_id')->get();
        $hotel_name_array= [];
        foreach ($hotel_name_list as $item){
            $hotel_name_array[$item->hotel_id] = ['hotel_name'=>$item->hotel_name, 'hotel_category'=>$item->hotel_category, 'hotel_stars'=>$item->hotel_stars , 'location'=>$item->location, 'booking_rating'=>$item->booking_rating, 'guests_favorite_area'=>$item->guests_favorite_area, 'self_verified'=>$item->self_verified];
        }

        $property_urls = PropertyUrl::select('hotel_id','url')->get();
        $property_url_array = [];
        foreach ($property_urls as $item){
            $property_url_array[$item->hotel_id] = $item->url;
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

        if(count($request->get('hotel_names'))>0){
            $hotel_name = $request->get('hotel_names');
            $hotelmaster = $hotelmaster->where(function ($query) use ($hotel_name) {
                foreach($hotel_name as $key => $name){
                    if($key == 0){
                        $query = $query->where('hotel_name', $name);
                    }else{
                        $query = $query->orWhere('hotel_name', $name);
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

        if($request->get('self_verified')!=Null && $request->get('self_verified')!=''){
            $is_verified = $request->get('self_verified');
            if($is_verified == '1'){
                $hotelmaster = $hotelmaster->Where('self_verified','>',0);
            } else if($is_verified == '0'){
                $hotelmaster = $hotelmaster->where(function ($query) {
                    $query = $query->whereNull('self_verified');
                    $query = $query->orWhere('self_verified','=',0);
                });
            }
        }

        if($request->get('guest_favourite')!=Null && $request->get('guest_favourite')!=''){
            $is_favourite = $request->get('guest_favourite');
            if($is_favourite == '1'){
                $hotelmaster = $hotelmaster->Where('guests_favorite_area','>',0);
            } else if($is_favourite == '0'){
                $hotelmaster = $hotelmaster->where(function ($query) {
                    $query = $query->whereNull('guests_favorite_area');
                    $query = $query->orWhere('guests_favorite_area','=',0);
                });
            }
        }

        if(count($request->get('stars'))>0 || count($request->get('ratings'))>0 || count($request->get('countries'))>0 || count($request->get('cities'))>0 || count($request->get('hotel_names'))>0 || count($request->get('categories'))>0 || ($request->get('self_verified')!=Null && $request->get('self_verified')!='') || ($request->get('guest_favourite')!=Null && $request->get('guest_favourite')!='')){
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

        if(count($request->get('available_only'))>0){
            $available_only = $request->get('available_only');
            $hotelprices = $hotelprices->where(function ($query) use ($available_only) {
                foreach($available_only as $key => $availableonly){
                    if($key == 0){
                        $query = $query->where('available_only', intval($availableonly));
                    }else{
                        $query = $query->orWhere('available_only', intval($availableonly));
                    }
                }
                return $query;
            });
        }

        if($request->get('guest_available')!=Null && $request->get('guest_available')!=''){
            $is_guest_available = $request->get('guest_available');
            if($is_guest_available == 'empty'){
                $hotelprices = $hotelprices->whereNull('number_of_guests')->orWhere('number_of_guests','=',0);
            } else if($is_guest_available == 'not-empty'){
                $hotelprices = $hotelprices->whereNotNull('number_of_guests')->orWhere('number_of_guests','>',0);
            }
        }
        
        if($request->get('created_at_from') != Null && $request->get('created_at_from') != ''){
            $hotelprices = $hotelprices->where('created_at', '>=', Carbon::parse($request->get('created_at_from'))->startOfDay());
        }

        if($request->get('created_at_to') != Null && $request->get('created_at_to') != ''){
            $hotelprices = $hotelprices->where('created_at', '<=', Carbon::parse($request->get('created_at_to'))->endOfDay());
        }

        if($request->get('min_price') != Null && $request->get('min_price') != ''){
            $hotelprices = $hotelprices->where('raw_price','>=',(int)$request->get('min_price'));
        }

        if($request->get('max_price') != Null && $request->get('max_price') != '' && $request->get('max_price') <= 500){
            $hotelprices = $hotelprices->where('raw_price','<=',(int)$request->get('max_price'));
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

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        // Code To Test The Mongodb Aggregate Query

        // $hotel_stars = $request->get('stars');
        // $hotel_rating = $request->get('ratings');
        // $country = $request->get('countries');
        // $city = $request->get('cities');
        // $self_verified = $request->get('self_verified');
        // $guest_favourite = $request->get('guest_favourite');
        // $hotel_category = $request->get('categories');
        // $hotel_name = $request->get('hotel_names');

        // $result = $hotelprices->raw(function($collection) use($hotel_stars, $hotel_rating, $country, $city, $self_verified, $guest_favourite, $hotel_category, $hotel_name, $start, $limit) {
        //                     return $collection->aggregate(array(
        //                         array( '$lookup' => array(
        //                             'from' => 'hotel_master',
        //                             'localField' => 'hotel_id',
        //                             'foreignField' => 'hotel_id',
        //                             'as' => 'hotelmaster'
        //                         )),
        //                         array( '$unwind' => array( 
        //                             'path' => '$hotelmaster', 'preserveNullAndEmptyArrays' => True
        //                         )),
        //                         array( '$match' => array(
        //                             '$or' => array(
        //                                 array('hotelmaster.hotel_stars' => array('$in',$hotel_stars ))
        //                             )
        //                         )),
        //                         array( '$skip' => intval($start)),
        //                         array( '$limit' => intval($limit))
        //                         ));
        //                     });
        // print_r(json_encode($result));
        // exit();

        #############################################################################
        if($request->get('export') != null && $request->get('export') == 'csv'){
            $hotelprices_data = $hotelprices->select('*')->offset(intval($start))
                         ->limit(intval(config('app.data_export_row_limit')))
                         ->orderBy($order,$dir)
                         ->get();             
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=file.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $callback = function() use ($hotelprices_data, $columns, $columns_header, $hotel_name_array)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns_header);

                foreach($hotelprices_data as $row) {
                    $row->checkin_date =  $row->checkin_date->toDateTime()->format('Y-m-d');
                    $row->raw_price =  str_replace(".",",",$row->raw_price);
                    $row->hotel_title = $hotel_name_array[$row->hotel_id]['hotel_name'];
                    $row->hotel_category = $hotel_name_array[$row->hotel_id]['hotel_category'];
                    $row->hotel_stars = $hotel_name_array[$row->hotel_id]['hotel_stars'];
                    $row->location = $hotel_name_array[$row->hotel_id]['location'];
                    $row->booking_rating = $hotel_name_array[$row->hotel_id]['booking_rating'];
                    $row->guests_favorite_area = $hotel_name_array[$row->hotel_id]['guests_favorite_area'];
                    $row->self_verified = $hotel_name_array[$row->hotel_id]['self_verified'];
                    if(count($row->other_desc) > 0){
                        $row->other_desc =  join("|",$row->other_desc);
                    }
                    else{
                        $row->other_desc =  '';
                    }
                    $data_row = [];
                    foreach ($columns as $key) {
                        array_push($data_row, $row->{$key});
                    }
                    fputcsv($file, $data_row);
                }
                fclose($file);
            };
            return Response::stream($callback, 200, $headers);
        }#############################################################################
        else{
            $room_array = [];
            $hotelprices_roomtype = clone $hotelprices;
            $room_type_array = $hotelprices_roomtype->select('room_type')->distinct()->get()->toarray();
            for($i=0; $i < count($room_type_array); $i++)
            {
                $temp_array = [];
                $temp_array['id'] = $room_type_array[$i][0];
                $temp_array['text'] = $room_type_array[$i][0];
                array_push($room_array,$temp_array);
            }
            
            $statistics = [];
            $statistics['avg_price'] = $hotelprices->avg('raw_price') ?: 0;
            $statistics['max_price'] = $hotelprices->max('raw_price') ?: 0;
            $statistics['min_price'] = $hotelprices->min('raw_price') ?: 0;

            $totalData = $hotelprices->count();
            $totalFiltered = $totalData;

            $hotelprices_data = $hotelprices->select('*')->offset(intval($start))
                         ->limit(intval($limit))
                         ->orderBy($order,$dir)
                         ->get();
        }

        for($i=0; $i < count($hotelprices_data); $i++)
        {
            $hotelprices_data[$i]['hotel_title'] = '<a class="hotel_equip_popup" hotel-id="'.$hotelprices_data[$i]['hotel_id'].'" title="hotel equipment" data-title="' . $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['hotel_name'] . '">' . $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['hotel_name'] . ' <i class="fa fa-info-circle"></i></a>';

            $hotelprices_data[$i]['hotel_category'] = $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['hotel_category'];
            $hotelprices_data[$i]['hotel_stars'] = $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['hotel_stars'];
            $hotelprices_data[$i]['location'] = $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['location'];
            $hotelprices_data[$i]['booking_rating'] = $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['booking_rating'];
            $hotelprices_data[$i]['guests_favorite_area'] = $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['guests_favorite_area'];
            $hotelprices_data[$i]['self_verified'] = $hotel_name_array[$hotelprices_data[$i]['hotel_id']]['self_verified'];

            $hotelprices_data[$i]['room_type'] = '<a class="room_equip_popup" hotel-id="'.$hotelprices_data[$i]['hotel_id'].'" title="room equipment" data-title="' . $hotelprices_data[$i]['room_type'] . '">' . $hotelprices_data[$i]['room_type'] . ' <i class="fa fa-info-circle"></i></a>';
            $hotelprices_data[$i]['raw_price'] = str_replace(".",",",$hotelprices_data[$i]['raw_price']);


            $checkin_date = $hotelprices_data[$i]['checkin_date']->toDateTime()->format('Y-m-d');
            $checkout_date = Carbon::parse($hotelprices_data[$i]['checkin_date']->toDateTime()->format('Y-m-d'))->addDays($hotelprices_data[$i]['number_of_days'])->format('Y-m-d');

            $url = $property_url_array[$hotelprices_data[$i]['hotel_id']] . "?checkin=" . $checkin_date . "&checkout=" . $checkout_date . "&selected_currency=EUR&group_adults=" . $hotelprices_data[$i]['number_of_guests'];
            $hotelprices_data[$i]['checkin_date'] =  '<a href="' . $url . '" target="_blank">' . $hotelprices_data[$i]['checkin_date']->toDateTime()->format('y-m-d') . "</a>";

            if($hotelprices_data[$i]['cancellation_desc']!= ''){
                $hotelprices_data[$i]['cancellation_type'] = $hotelprices_data[$i]['cancellation_type'] ." ( ".$hotelprices_data[$i]['cancellation_desc']. " )";    
            }
            else{
                $hotelprices_data[$i]['cancellation_type'] = $hotelprices_data[$i]['cancellation_type'];
            }
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $hotelprices_data,
                    "statistics"      => $statistics,
                    "room_array"      => $room_array
                    );
            
        echo json_encode($json_data);
    }

    public function getHotelEquipment(Request $request)
    {
        $hotel_master = HotelMaster::select('hotel_equipments')->where('hotel_id',$request->get('hotel_id'))->first();
        return response()->json(["status"=>"success","data"=>$hotel_master->hotel_equipments]);
    }

    public function getRoomEquipment(Request $request)
    {
        $room_types = RoomDetails::select('room_equipment')->where('room_type',$request->get('room_type'))->latest()->first();
        return response()->json(["status"=>"success", "data"=>$room_types->room_equipment]);
    }
}