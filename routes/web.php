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

//login
Route::get('/', 'Auth\LoginController@showLogin');

Route::post('login', array('uses' => 'Auth\LoginController@doLogin'));

Route::get('logout', 'Auth\LoginController@logout');

Route::get('reset', 'Auth\ForgotPasswordController@reset');

Route::get('select-location', 'Auth\LoginController@selectLocation');

Route::POST('loginWithLoc', 'Auth\LoginController@loginWithLoc');


Route::get('/recover', function () {
    return view('recover');
});

Route::get('/home', function () {
    return view('dashboard');
});

// Country module
Route::get('create_country','CountryController@index');
Route::get('get_all_country','CountryController@show');
Route::delete('delete_country/{country_id}','CountryController@delete');
Route::get('edit_country/{country_id}','CountryController@edit');
Route::post('update_country/{country_id}','CountryController@update');
Route::post('insertCountry','CountryController@insertCountry');

//GRN module
Route::get('grn_details','GrnController@grnDetails');


Route::get('register', function () {
    return view('user.register');
});


Route::post('register', array('uses'=>'UserController@store'));

//currency routes
Route::get('currency.new',['uses' => 'CurrencyController@new_currency']);

Route::post('currency.save','CurrencyController@save');

Route::get('currency.get_currency_list','CurrencyController@get_currency_list');

Route::get('currency.get','CurrencyController@get_currency');

//payment term routes
Route::get('payment-term.new','PaymentTermController@new_payment_term');

Route::post('payment-term.save','PaymentTermController@save');

Route::get('payment-term.get_payment_term_list','PaymentTermController@get_payment_term_list');

Route::get('payment-term.get','PaymentTermController@get_payment_term');


// add location

Route::get('add_location', function () { return view('org/location/add_location'); });

Route::post('Mainsource.postdata','Org\Location\MainSourceController@postdata');
Route::get('Mainsource.loaddata','Org\Location\MainSourceController@loaddata');
Route::get('Mainsource.check_code','Org\Location\MainSourceController@check_code');
Route::get('Mainsource.edit','Org\Location\MainSourceController@edit');
Route::get('Mainsource.delete','Org\Location\MainSourceController@delete');
Route::get('Mainsource.load_list','Org\Location\MainSourceController@select_Source_list');

Route::get('Maincluster.loaddata','Org\Location\MainClusterController@loaddata');
Route::get('Maincluster.check_code','Org\Location\Org\Location\MainClusterController@check_code');
Route::post('Maincluster.postdata','Org\Location\MainClusterController@postdata');
Route::get('Maincluster.edit','Org\Location\MainClusterController@edit');
Route::get('Maincluster.delete','Org\Location\MainClusterController@delete');

Route::get('Mainlocation.loaddata','Org\Location\MainLocationController@loaddata');
Route::get('Mainlocation.load_list','Org\Location\MainLocationController@select_loc_list');
Route::get('Mainlocation.load_currency','Org\Location\MainLocationController@load_currency');
Route::get('Mainlocation.load_country','Org\Location\MainLocationController@load_country');
Route::get('Mainlocation.check_code','Org\Location\MainLocationController@check_code');
Route::post('Mainlocation.postdata','Org\Location\MainLocationController@postdata');
Route::get('Mainlocation.edit','Org\Location\MainLocationController@edit');
Route::get('Mainlocation.delete','Org\Location\MainLocationController@delete');

Route::get('MainSubLocation.loaddata','Org\Location\MainSubLocationController@loaddata');
Route::get('MainSubLocation.load_list','Org\Location\MainSubLocationController@load_list');
Route::get('MainSubLocation.check_code','Org\Location\MainSubLocationController@check_code');
Route::post('MainSubLocation.postdata','Org\Location\MainSubLocationController@postdata');
Route::get('MainSubLocation.edit','Org\Location\MainSubLocationController@edit');
Route::get('MainSubLocation.delete','Org\Location\MainSubLocationController@delete');

// close add location