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

Route::get('/', 'Auth\LoginController@showLogin');

// route to process the form
Route::post('login', array('uses' => 'Auth\LoginController@doLogin'));

Route::get('logout', 'Auth\LoginController@logout');
Route::get('reset', 'Auth\ForgotPasswordController@reset');
//Route::get('home', 'Auth\LoginController@home');


Route::get('/recover', function () {
    return view('recover');
});

Route::get('/home', function () {
    return view('dashboard');
});

Route::get('register', function () {
    return view('user.register');
});


Route::post('register', array('uses'=>'UserController@store'));



//currency routes
Route::get('currency.new',['uses' => 'CurrencyController@new_currency2']);

Route::post('currency.save','CurrencyController@save');

Route::get('currency.get_currency_list','CurrencyController@get_currency_list');

Route::get('currency.get','CurrencyController@get_currency');

//payment term routes
Route::get('payment-term.new','PaymentTermController@new_payment_term');

Route::post('payment-term.save','PaymentTermController@save');

Route::get('payment-term.get_payment_term_list','PaymentTermController@get_payment_term_list');

Route::get('payment-term.get','PaymentTermController@get_payment_term');





