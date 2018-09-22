<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/property_url', 'PropertyUrlController@index')->name('property_url.index');
Route::get('/property_getdata', 'PropertyUrlController@getData')->name('property_url.index.getData');
Route::post('/property_url', 'PropertyUrlController@store');
Route::post('/property_url/updatestatus', 'PropertyUrlController@updatePropertyUrlStatus')->name('property_url.update_status');

Route::get('/hotel_master', 'HotelMasterController@index')->name('hotel_master.index');
Route::get('/hotel_master_getdata', 'HotelMasterController@getData')->name('hotel_master.getData');

Route::get('/hotel_prices', 'HotelPricesController@index')->name('hotel_prices.index');
Route::get('/hotel_prices_getdata', 'HotelPricesController@getData')->name('hotel_prices.index.getData');

Route::get('/room_details', 'RoomDetailsController@index')->name('room_details.index');
Route::get('/room_details_getdata', 'RoomDetailsController@getData')->name('room_details.index.getData');

Route::get('/rooms_availability', 'RoomsAvailabilityController@index')->name('rooms_availability.index');
Route::get('/rooms_availability_getdata', 'RoomsAvailabilityController@getData')->name('rooms_availability.index.getData');


Route::resource('users', 'UserController');
Route::get('/getdata', 'UserController@getData')->name('users.index.getdata');

Route::get('/config', 'HomeController@config')->name('config');
Route::post('/config', 'HomeController@configUpdate')->name('config_update');