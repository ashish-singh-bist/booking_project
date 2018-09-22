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
            PropertyUrl::create([ 'city' => $value[0], 'url' => $value[1] ]);
        }
        flash('CSV uploaded successfully!')->success()->important();
        return redirect()->route('property_url.index');
    }

    public function getData()
    {
        $propertyurl = PropertyUrl::all();
        return Datatables::of($propertyurl)
        ->addColumn('link', function ($propertyurl) {
            $html =  '<a href="' . route('hotel_master.index') . '?id=' . $propertyurl->_id . '" class="btn btn-xs btn-success" title="Info"><i class="fa fa-eye"></i> Info</a>';
            $html .=  '&nbsp;<a href="' . route('hotel_prices.index') . '?id=' . $propertyurl->_id . '" class="btn btn-xs btn-success" title="Price"><i class="fa fa-eye"></i> Price</a>';
            $html .=  '&nbsp;<a href="' . route('room_details.index') . '?id=' . $propertyurl->_id . '" class="btn btn-xs btn-success" title="Room Details"><i class="fa fa-eye"></i> Room Details</a>';
            $html .=  '&nbsp;<a href="' . route('rooms_availability.index') . '?id=' . $propertyurl->_id . '" class="btn btn-xs btn-success" title="Availability"><i class="fa fa-eye"></i> Availability</a>';
            return $html;
        })
        ->addColumn('action', function ($propertyurl) {
            if($propertyurl->is_active == '1'){
                $html = '&nbsp;<button  prop_id="'. $propertyurl->_id.'" status="1" class="btn btn-xs btn-success update_status" title="Active"><i class="fa fa-check"></i> Active</button>';
            }
            else{
                $html = '&nbsp;<button  prop_id="'. $propertyurl->_id.'" status="0" class="btn btn-xs btn-danger update_status" title="Inactive"><i class="fa fa-close"></i> Inactive</button>';
            }
            return $html;
        })
        ->rawColumns([ 'link','action' ])
        ->make(true);
    }

    public function updatePropertyUrlStatus(Request $request)
    {
        $property_url = PropertyUrl::find($request->_id);
        $property_url->is_active = $request->is_active;
        $property_url->save();
        return  response()->json([
            'status' =>true,
            'message' => 'Property Url Status Updated'
        ]);
    }
}