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

/*
routing responses and codes
...................................................
HTTP_OK = 200;
HTTP_CREATED = 201;
HTTP_NO_CONTENT = 204;
HTTP_BAD_REQUEST = 400;
HTTP_UNAUTHORIZED = 401;
HTTP_NOT_FOUND = 404;
HTTP_METHOD_NOT_ALLOWED = 405;
HTTP_CONFLICT = 409;
HTTP_INTERNAL_SERVER_ERROR = 500;
*/


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

  Route::get('countries/validate' , 'Org\CountryController@validate_data');
  Route::apiResource('countries','Org\CountryController');

  Route::get('sections/validate' , 'Org\SectionController@validate_data');
  Route::apiResource('sections','Org\SectionController');

  Route::get('departments/validate' , 'Org\DepartmentController@validate_data');
  Route::apiResource('departments','Org\DepartmentController');

  Route::get('sources/validate' , 'Org\Location\SourceController@validate_data');
  Route::apiResource('sources','Org\Location\SourceController');

  Route::get('clusters/validate' , 'Org\Location\ClusterController@validate_data');
  Route::apiResource('clusters','Org\Location\ClusterController');

  Route::get('companies/validate' , 'Org\Location\CompanyController@validate_data');
  Route::apiResource('companies','Org\Location\CompanyController');

  Route::get('locations/validate' , 'Org\Location\LocationController@validate_data');
  Route::apiResource('locations','Org\Location\LocationController');

  Route::apiResource('location-types','Org\LocationTypeController');
  Route::apiResource('property-types','Org\PropertyTypeController');

  Route::get('customers/validate' , 'Org\CustomerController@validate_data');
  Route::get('customers/divisions' , 'Org\CustomerController@customer_divisions');
  Route::put('customers/divisions' , 'Org\CustomerController@save_customer_divisions');
  Route::apiResource('customers','Org\CustomerController');

  Route::get('suppliers/validate' , 'Org\SupplierController@validate_data');
  Route::apiResource('suppliers','Org\SupplierController');

  Route::get('uom/validate' , 'Org\UOMController@validate_data');
  Route::apiResource('uom','Org\UOMController');

  Route::get('cancellation-categories/validate' , 'Org\Cancellation\CancellationCategoryController@validate_data');
  Route::apiResource('cancellation-categories','Org\Cancellation\CancellationCategoryController');

  Route::get('cancellation-reasons/validate' , 'Org\Cancellation\CancellationReasonController@validate_data');
  Route::apiResource('cancellation-reasons','Org\Cancellation\CancellationReasonController');

  Route::get('divisions/validate' , 'Org\DivisionController@validate_data');
  Route::apiResource('divisions','Org\DivisionController');

  Route::get('seasons/validate' , 'Org\SeasonController@validate_data');
  Route::apiResource('seasons','Org\SeasonController');

  Route::get('origin-types/validate' , 'Org\OriginTypeController@validate_data');
  Route::apiResource('origin-types','Org\OriginTypeController');

  Route::get('sizes/validate' , 'Org\SizeController@validate_data');
  Route::apiResource('sizes','Org\SizeController');

  Route::get('colors/validate' , 'Org\ColorController@validate_data');
  Route::apiResource('colors','Org\ColorController');

  Route::get('stores/validate' , 'Org\StoreController@validate_data');
  Route::apiResource('stores','Org\StoreController');

});


  Route::apiResource('ship-modes','Org\ShipModeController');


Route::prefix('items/')->group(function(){
    Route::get('itemlists/loadItemList' , 'itemCreationController@GetItemList');
    Route::apiResource('itemlists','itemCreationController');
});




Route::prefix('finance/')->group(function(){

  Route::get('goods-types/validate' , 'Finance\GoodsTypeController@validate_data');
  Route::apiResource('goods-types','Finance\GoodsTypeController');

  Route::get('ship-terms/validate' , 'Finance\ShipmentTermController@validate_data');
  Route::apiResource('ship-terms','Finance\ShipmentTermController');

  Route::get('accounting/payment-methods/validate' , 'Finance\Accounting\PaymentMethodController@validate_data');
  Route::apiResource('accounting/payment-methods','Finance\Accounting\PaymentMethodController');

  Route::get('accounting/payment-terms/validate' , 'Finance\Accounting\PaymentTermController@validate_data');
  Route::apiResource('accounting/payment-terms','Finance\Accounting\PaymentTermController');

  Route::get('accounting/cost-centers/validate' , 'Finance\Accounting\CostCenterController@validate_data');
  Route::apiResource('accounting/cost-centers','Finance\Accounting\CostCenterController');

  Route::get('currencies/validate' , 'Finance\CurrencyController@validate_data');
  Route::apiResource('currencies','Finance\CurrencyController');

  Route::get('exchange-rates/validate' , 'Finance\ExchangeRateController@validate_data');
  Route::apiResource('exchange-rates','Finance\ExchangeRateController');

});


Route::prefix('stores/')->group(function(){

  Route::apiResource('po-load','stores\RollPlanController');

});

Route::prefix('merchandising/')->group(function(){

//  Route::get('g/validate' , 'Finance\GoodsTypeController@validate_data');
    Route::apiResource('customer-orders','Merchandising\CustomerOrderController');
    Route::apiResource('customer-order-details','Merchandising\CustomerOrderDetailsController');
    Route::apiResource('customer-order-types','Merchandising\CustomerOrderTypeController');
    Route::apiResource('get-style','Merchandising\StyleCreationController');
    Route::apiResource('tna-master','Merchandising\TnaMasterController');


});

Route::prefix('admin/')->group(function(){
  Route::get('permission/validate' , 'Admin\PermissionController@validate_data');
  Route::apiResource('permission','Admin\PermissionController');
});


Route::prefix('core/')->group(function(){

  Route::apiResource('status','Core\StatusController');

});

//Route::group(['middleware' => ['jwt.auth']], function() {

  Route::GET('/sources','Test\SourceController@index');
  Route::GET('/getCustomer','Org\CustomerController@loadCustomer');
  Route::GET('/getProductCategory','Merchandising\ProductCategoryController@loadProductCategory');
  Route::GET('/getProductType','Merchandising\ProductTypeController@loadProductType');
  Route::GET('/getProductFeature','Merchandising\ProductFeatureController@loadProductFeature');
  Route::GET('/getProductSilhouette','Merchandising\ProductSilhouetteController@loadProductSilhouette');

  Route::POST('/style-creation.save','Merchandising\styleCreationController@saveStyleCreation');

  /*Route::post('/sources','Test\SourceController@index');

    Route::get('logout', 'AuthController@logout');
    Route::get('test', function(){
        return response()->json(['foo'=>'bar']);
    });*/
//});
