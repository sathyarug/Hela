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

/*Route::get('/', function () {
    return view('welcome');
});*/

//Route::get('/', function () {
//    try {
//      print_r(DB::connection()->getPdo());
//    } catch (\Exception $e) {
//        die("Could not connect to the database.  Please check your configuration.");
//    }
//    return view('dashboard');
//});

Route::get('/', 'Auth\LoginController@showLogin');

// route to process the form
Route::post('login', array('uses' => 'Auth\LoginController@doLogin'));
