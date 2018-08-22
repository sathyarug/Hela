<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

//org routing.................................

/*
GET|HEAD  | api/countries  | countries.index    | App\Http\Controllers\Org\CountryController@index
POST      | api/countries | countries.store    | App\Http\Controllers\Org\CountryController@store
PUT|PATCH | api/countries/{country}  | countries.update   | App\Http\Controllers\Org\CountryController@update
GET|HEAD  | api/countries/{country}  | countries.show     | App\Http\Controllers\Org\CountryController@show
DELETE    | api/countries/{country}  | countries.destroy  | App\Http\Controllers\Org\CountryController@destroy */
Route::prefix('org/')->group(function(){
  Route::get('countries/list' , 'Org\CountryController@list');
  Route::get('countries/search' , 'Org\CountryController@search');
  Route::get('countries/check-code' , 'Org\CountryController@check_code');
  Route::apiResource('countries','Org\CountryController');
});



//Route::group(['middleware' => ['jwt.auth']], function() {
  Route::post('/sources','Test\SourceController@index');
    Route::get('logout', 'AuthController@logout');
    Route::get('test', function(){
        return response()->json(['foo'=>'bar']);
    });
//});

//Route::apiResource('/sources','Test\SourceController');
