<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use JsValidator;
use App\RoomDetails;

class RoomDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {   
        if($request->get('id') != Null && $request->get('id') != ''){
            return view('room_details.index',['id'=>$request->get('id')]);
        }else{
            return view('room_details.index');
        }          
    }

    public function getData(Request $request)
    {
        $roomdetails = new RoomDetails();
        if($request->get('id') != Null && $request->get('id') != ''){
            $roomdetails = $roomdetails->where('prop_id',new \MongoDB\BSON\ObjectID($request->get('id')));
        }         
        if($request->get('room_type') != Null && $request->get('room_type') != ''){
            $roomdetails = $roomdetails->where('room_type',$request->get('room_type'));
        }          
        $roomdetails_data = $roomdetails->get();
        return Datatables::of($roomdetails_data)->make(true);        
    }
}