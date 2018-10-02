<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use JsValidator;
use App\PropertyUrl;

class PropertyUrlController extends Controller
{   
    /*
    |--------------------------------------------------------------------------
    | PropertyUrlController Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles user related task in admin panel (create, edit, update and delete user).
    |
    */
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('property_url.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Code To Save Details in MySQL Database and mongodb the syntax are same
        $path = $request->file('property_url_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        foreach ($data as $key => $value) {
            if ($key == 0) continue;
            PropertyUrl::create([ 'city' => $value[0], 'url' => $value[1], 'is_active' => 1 ]);
        }
        flash('CSV uploaded successfully!')->success()->important();
        return redirect()->route('property_url.index');
    }

    public function getData(Request $request)
    {
        $columns = ['hotel_name', 'hotel_id', 'city', 'created_at', 'updated_at', 'link', 'action'];

        $propertyurl = new PropertyUrl();

        if(count($request->get('countries'))>0){
            $countries = $request->get('countries');
            $propertyurl = $propertyurl->where(function ($query) use ($countries) {
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
            $propertyurl = $propertyurl->where(function ($query) use ($cities) {
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

        $totalData = $propertyurl->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $propertyurl_data = $propertyurl->offset(intval($start))
                     ->limit(intval($limit))
                     ->orderBy($order,$dir)
                     ->get();

        for($i=0; $i < count($propertyurl_data); $i++)
        {
            $link_hotel_name = '';
            if($propertyurl_data[$i]->hotel_name != '' && $propertyurl_data[$i]->hotel_name != Null){
                $link_hotel_name =  '<a href="' . $propertyurl_data[$i]->url . '" target="_blank" title="'. $propertyurl_data[$i]->hotel_name .'">'. $propertyurl_data[$i]->hotel_name .'</a>';
            }else{
                $link_hotel_name =  '<a href="' .$propertyurl_data[$i]->url . '" target="_blank" title="'. $propertyurl_data[$i]->url .'">'. $propertyurl_data[$i]->url .'</a>';
            }
            $link_html= '';            
            if(isset($propertyurl_data[$i]->hotel_id)){
                $link_html =  '<a href="' . route('hotel_master.index') . '?id=' . $propertyurl_data[$i]->hotel_id. '" class="btn btn-xs btn-success" title="Hotel Details"><i class="fa fa-info fa-size"></i></a>';
                $link_html .=  '&nbsp;<a href="' . route('hotel_prices.index') . '?id=' .$propertyurl_data[$i]->hotel_id . '" class="btn btn-xs btn-success" title="Hotel Prices"><i class="fa fa-euro fa-size"></i></a>';
                $link_html .=  '&nbsp;<a href="' . route('room_details.index') . '?id=' . $propertyurl_data[$i]->hotel_id . '" class="btn btn-xs btn-success" title="Room Details"><i class="fa fa-home fa-size"></i></a>';
                // $link_html .=  '&nbsp;<a href="' . route('rooms_availability.index') . '?id=' . $propertyurl_data[$i]->hotel_id . '" class="btn btn-xs btn-success" title="Room Availability"><i class="fa fa-font fa-size"></i></a>';
            }

            // $action_html = '';
            // if($propertyurl_data[$i]->is_active == 1){
            //     $action_html = '&nbsp;<button  prop_id="'. $propertyurl_data[$i]->_id.'" status="1" class="btn btn-xs btn-success update_status" title="Active"><i class="fa fa-check"></i> Active</button>';
            // }
            // else if($propertyurl_data[$i]->is_active == 0){
            //     $action_html = '&nbsp;<button  prop_id="'. $propertyurl_data[$i]->_id.'" status="0" class="btn btn-xs btn-danger update_status" title="Inactive"><i class="fa fa-close"></i> Inactive</button>';
            // }

            $static_array = [0,1,2,3,4,5,6,7,14,30];
            $action_html = '&nbsp;<select prop_id="'. $propertyurl_data[$i]->_id.'" class="form-control filter_class update_status" title="Active">';
            for($j=0;$j<count($static_array);$j++){
                if($j == $propertyurl_data[$i]->is_active){
                    if($static_array[$j] == 0){
                        $action_html.= '<option name="'.$static_array[$j].'" value="'.$static_array[$j].' selected ">'.$static_array[$j].' (inactive)</option>';
                    }
                    else {
                        $action_html.= '<option name="'.$static_array[$j].'" value="'.$static_array[$j].'" selected>'.$static_array[$j].' days</option>';
                    }
                    
                }else{
                    if($static_array[$j] == 0){
                        $action_html.= '<option name="'.$static_array[$j].'" value="'.$static_array[$j].'">'.$static_array[$j].' (inactive)</option>';
                    }
                    else{
                        $action_html.= '<option name="'.$static_array[$j].'" value="'.$static_array[$j].'">'.$static_array[$j].' days</option>';
                    }
                }
            }
            $action_html .= '</select>';
            $propertyurl_data[$i]['hotel_name'] = $link_hotel_name;
            $propertyurl_data[$i]['link'] = $link_html;
            $propertyurl_data[$i]['action'] = $action_html;
        }
        
        $json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $propertyurl_data,
                    );
            
        echo json_encode($json_data);
    }

    public function updatePropertyUrlStatus(Request $request)
    {
        $property_url = PropertyUrl::find($request->_id);
        $property_url->is_active = intval($request->is_active);
        $property_url->save();
        return  response()->json([
            'status' =>true,
            'message' => 'Property Url Status Updated'
        ]);
    }
}