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