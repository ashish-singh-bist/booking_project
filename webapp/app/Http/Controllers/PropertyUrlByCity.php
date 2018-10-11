<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use JsValidator;
use App\PropertyUrl;
use GuzzleHttp\Client;

class PropertyUrlByCity extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('property_url.property_url_by_city');
    }

    public function getPropertyUrlsByCity(Request $request)
    {
    	$city = $request->get('city');
        $response_data = [];
		if($city!=null || $city!=''){
            $url = "https://www.booking.com/searchresults.de.html?dest_type=city&dtdisc=0&from_sf=1&group_adults=2&group_children=0&inac=0&index_postcard=0&label_click=undef&no_rooms=1&postcard=0&raw_dest_type=city&ss=" . urlencode($city) . "&ssb=empty&sshis=0&order=popularity";

            //Guzzle Code
            $guzzle_client = new Client();
            $guj_request = $guzzle_client->get($url);
            if($guj_request->getStatusCode() == 200){
             $html = $guj_request->getBody();
             $response_data = $this->parseProperties($html);
            }
		}
		$property_url_city = [];
		for($i=0; $i < count($response_data); $i++){
			$property_url_city[$i]['action'] = '';
			$property_url_city[$i]['url'] = "https://www.booking.com/" . $response_data[$i]['hotel_url'];
		}

		$json_data = array(
	                    "draw" => intval($request->input('draw')),
	                    "data" => $property_url_city,
	                    "city" => $city
                    );
            
        echo json_encode($json_data);
    }

    public function savePropertyUrlDetails(Request $request)
    {
    	$property_urls =  $request->get('url');
    	$city = $request->get('city');
    	foreach($property_urls as $url){
    		$property_url_obj = new PropertyUrl();
    		$property_url_obj->url = $url;
    		$property_url_obj->city = $city;
            $property_url_obj->parse_interval = 1;
    		$property_url_obj->save();
    	}
    	$property_url_city = [];
    	//flash('Property url saved successfully!')->success()->important();
    	return  response()->json([
            'status' =>true,
            'message' => 'Property Url Status Updated'
        ]);
    }

    function parseProperties($html)
    {
        $property_array = array();
        preg_match_all("/(<div[^>]+data-hotelid[^>]*>.+?class\W+(sr_rooms_table_block|room_details))/ims", $html, $tempmatches, PREG_SET_ORDER);
        foreach ($tempmatches as $tempval) 
        {
            $name='';$address='';$hotel_url='';$img_url='';$rating='';$review_count='';
            $property = array();
            $str=$tempval[1]."<br>";
            #<span class="sr-hotel__name" data-et-click="">SchlafGut AppartementHotel</span>
            if(preg_match("/<span[^>]+class\W+sr-hotel__name\W[^>]*>(.+?)<\/span>/is",$str,$temparr))
            {
                $name=trim($temparr[1]);
            }
            #<div class="bui-review-score__text"> 189 reviews </div>
            if(preg_match("/<div[^>]+class\W+bui-review-score__text\W[^>]*>(.+?)<\/div>/is",$str,$temparr))
            {
                $review_count=trim($temparr[1]);
            }
            ##<div class="bui-review-score__badge" role="link" aria-label="7.6"> 7.6 </div>
            if(preg_match("/<div[^>]+class\W+bui-review-score__badge\W[^>]*>(.+?)<\/div>/is",$str,$temparr))
            {
                $rating=trim($temparr[1]);
            }
            #<img class="hotel_image" src="https://t-ec.bstatic.com/xdata/images/hotel/square200/123324327.jpg?k=527c64214efbda20a3051d59a919eca4ec104dec6936088c3b0aaa4be4d7558b&amp;o=" alt="
            if(preg_match("/(<img[^>]+class\W+hotel_image\W[^>]*)/is",$str,$temparr))
            {
                $img_str=$temparr[1];
                if(preg_match("/src\W+([^\'\"]+)/i",$img_str,$temparr))
                {
                    $img_url=trim($temparr[1]);
                }
            }
            if(preg_match("/(<a[^>]+class\W+[^\"\']*hotel_name_link\W[^>]+>)/is",$str,$temparr))
            {
                $hotel_url_str=$temparr[1];
                if(preg_match("/href\W+([^\'\"]+)/i",$hotel_url_str,$temparr))
                {
                    $hotel_url=trim($temparr[1]);
                    $hotel_url=substr($hotel_url,0,strpos($hotel_url,"?",0));
                }
            }
            $property['name'] = $name;
            $property['review_count'] = $review_count;
            $property['rating'] = $rating;
            $property['img_url'] = $img_url;
            $property['hotel_url'] = $hotel_url;

            array_push($property_array,$property);
            
        }## end for each
        return $property_array;
    }    
}
