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

Route::get('/icon', function () {
    return view('icon_page');
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

//cost center routes
Route::get('cost-center.new','Org\CostCenterController@new');

Route::post('cost-center.save','Org\CostCenterController@save');

Route::get('cost-center.get_list','Org\CostCenterController@get_list');

Route::get('cost-center.get','Org\CostCenterController@get');

// add location

Route::get('add_location', function () { return view('add_location/add_location'); });

Route::post('Mainsource.postdata','MainSourceController@postdata');

Route::get('Mainsource.loaddata','MainSourceController@loaddata');

Route::get('Mainsource.check_code','MainSourceController@check_code');

Route::get('Mainsource.edit','MainSourceController@edit');

Route::get('Mainsource.delete','MainSourceController@delete');

Route::get('Mainsource.load_list','MainSourceController@select_Source_list');

Route::get('Maincluster.loaddata','MainClusterController@loaddata');

Route::get('Mainlocation.loaddata','MainLocationController@loaddata');

// close add location


// supplier
Route::get('supplier', 'SupplierController@view');


Route::resource('admin/permission', 'Admin\\PermissionController');

Route::post('admin/role/getList', 'Admin\\RoleController@getList');
Route::resource('admin/role', 'Admin\\RoleController');
