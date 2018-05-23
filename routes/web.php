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

Route::get('/', function () {
    return view('dashboard');
});


Route::get('icon', function () {
    return view('icon_page');
});


Route::get('dashboard', function () {
    return view('dashboard');
});


Route::get('alert', function () {
    return view('alert');
});


Route::get('costing', function () {
    return view('costing');
});

Route::get('add_location', function () {
    return view('add_location/add_location');
});







