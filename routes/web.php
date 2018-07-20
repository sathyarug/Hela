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

Route::get('add_country','CountryController@index');
Route::get('get_all_country','CountryController@loaddata');
Route::get('check_code','CountryController@checkCode');
Route::post('save_country','CountryController@saveCountry');
Route::get('edit_country','CountryController@edit');
Route::get('delete_country','CountryController@delete');

//Division module
Route::get('add_division','DivisionController@index');
Route::get('check_division_code','DivisionController@checkCode');
Route::get('get_all_division','DivisionController@loadData');
Route::post('save_division','DivisionController@saveDivision');
Route::get('edit_division','DivisionController@edit');
Route::get('delete_division','DivisionController@delete');

//Season module
Route::get('add_season','SeasonController@index');
Route::get('check_season_code','SeasonController@checkCode');
Route::get('get_all_season','SeasonController@loadData');
Route::post('save_season','SeasonController@saveSeason');
Route::get('edit_season','SeasonController@edit');
Route::get('delete_season','SeasonController@delete');

//UOM module
Route::get('add_uom','UomController@index');
Route::get('check_uom_code','UomController@checkCode');
Route::get('get_all_uom','UomController@loadData');
Route::post('save_uom','UomController@saveUom');
Route::get('edit_uom','UomController@edit');
Route::get('delete_uom','UomController@delete');

//Section module
Route::get('add_section','SectionController@index');
Route::get('check_section_code','SectionController@checkCode');
Route::get('get_all_section','SectionController@loadData');
Route::post('save_section','SectionController@saveSection');
Route::get('edit_section','SectionController@edit');
Route::get('delete_section','SectionController@delete');

//Cancellation category module
Route::get('add_category','Org\Cancellation\CancellationCategoryController@index');
Route::get('check_category_code','Org\Cancellation\CancellationCategoryController@checkCode');
Route::get('get_all_category','Org\Cancellation\CancellationCategoryController@loadData');
Route::post('save_category','Org\Cancellation\CancellationCategoryController@saveCategory');
Route::get('edit_category','Org\Cancellation\CancellationCategoryController@edit');
Route::get('delete_category','Org\Cancellation\CancellationCategoryController@delete');

//Cancellation Reason module
Route::get('add_reason','Org\Cancellation\CancellationReasonController@index');
Route::get('check_reason_code','Org\Cancellation\CancellationReasonController@checkCode');
Route::get('get_all_reason','Org\Cancellation\CancellationReasonController@loadData');
Route::post('save_reason','Org\Cancellation\CancellationReasonController@saveReason');
Route::get('edit_reason','Org\Cancellation\CancellationReasonController@edit');
Route::get('delete_reason','Org\Cancellation\CancellationReasonController@delete');
//GRN module
Route::get('grn_details','GrnController@grnDetails');

//User Management
Route::get('admin/register', 'Admin\\UserController@register');
Route::get('admin/user', 'Admin\\UserController@user');
Route::post('admin/register-user', 'Admin\\UserController@store');
Route::get('admin/validate-empno', 'Admin\\UserController@validateEmpNo');
Route::get('admin/validate-username', 'Admin\\UserController@validateUserName');
Route::get('admin/load-report-levels', 'Admin\\UserController@loadReportLevels');
Route::post('admin/get-user-list', 'Admin\\UserController@getUserList');

Route::get('register', function () {
    return view('user.register');
});


Route::post('register-user', 'UserController@store');

//currency routes
Route::get('currency.new',['uses' => 'CurrencyController@new_currency']);

Route::post('currency.save','CurrencyController@save');

Route::get('currency.get_currency_list','CurrencyController@get_currency_list');

Route::get('currency.get','CurrencyController@get_currency');


Route::get('accounting-rules', function () { return view('finance/accounting/accounting_rules'); });

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

Route::get('add_location', function () { return view('org/location/add_location'); });

Route::post('Mainsource.postdata','Org\Location\MainSourceController@postdata');
Route::get('Mainsource.loaddata','Org\Location\MainSourceController@loaddata');
Route::get('Mainsource.check_code','Org\Location\MainSourceController@check_code');
Route::get('Mainsource.edit','Org\Location\MainSourceController@edit');
Route::get('Mainsource.delete','Org\Location\MainSourceController@delete');
Route::get('Mainsource.load_list','Org\Location\MainSourceController@select_Source_list');

Route::get('Maincluster.loaddata','Org\Location\MainClusterController@loaddata');
Route::get('Maincluster.check_code','Org\Location\MainClusterController@check_code');
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
Route::get('Mainlocation.load_section_list','Org\Location\MainLocationController@load_section_list');
Route::post('Mainlocation.save_section','Org\Location\MainLocationController@save_section');
Route::get('Mainlocation.section','Org\Location\MainLocationController@edit_load_section');
Route::get('Mainlocation.load_depat_list','Org\Location\MainLocationController@load_depat_list');

Route::get('MainSubLocation.loaddata','Org\Location\MainSubLocationController@loaddata');
Route::get('MainSubLocation.load_list','Org\Location\MainSubLocationController@load_list');
Route::get('MainSubLocation.check_code','Org\Location\MainSubLocationController@check_code');
Route::post('MainSubLocation.postdata','Org\Location\MainSubLocationController@postdata');
Route::get('MainSubLocation.edit','Org\Location\MainSubLocationController@edit');
Route::get('MainSubLocation.delete','Org\Location\MainSubLocationController@delete');
Route::get('MainSubLocation.type_of_loc','Org\Location\MainSubLocationController@type_of_loc');
Route::get('MainSubLocation.load_cost_center','Org\Location\MainSubLocationController@load_cost_center');
Route::get('MainSubLocation.load_property','Org\Location\MainSubLocationController@load_property');
// close add location

// Department
Route::get('department', function () { return view('org/department/department'); });
Route::post('Department.save','Org\DepartmentController@save_dep');
Route::get('Department.check_department','Org\DepartmentController@check_department');
Route::get('Department.loaddata','Org\DepartmentController@loaddata');
Route::get('Department.edit','Org\DepartmentController@edit');
Route::get('Department.delete','Org\DepartmentController@delete');

// supplier
Route::get('supplier', 'SupplierController@view');
Route::post('supplier/getList', 'SupplierController@getList');
Route::post('supplier/save', 'SupplierController@saveSupplier');


Route::get('admin/permission/checkName', 'Admin\\PermissionController@checkName');
Route::post('admin/permission/getList', 'Admin\\PermissionController@getList');
Route::post('admin/permission/{id}', 'Admin\\PermissionController@update');
Route::delete('admin/permission/{id}', 'Admin\\PermissionController@destroy');
Route::resource('admin/permission', 'Admin\\PermissionController');

Route::get('admin/role/checkName', 'Admin\\RoleController@checkName');
Route::post('admin/role/getList', 'Admin\\RoleController@getList');
Route::post('admin/role/{id}', 'Admin\\RoleController@update');
Route::delete('admin/role/{id}', 'Admin\\RoleController@destroy');
Route::resource('admin/role', 'Admin\\RoleController');

//cost center routes sub-category-check-code
Route::get('item','Finance\Item\ItemSubCategoryController@new');
Route::get('sub-category-check-code' , 'Finance\Item\ItemSubCategoryController@check_sub_category_code');
Route::post('sub-category-save','Finance\Item\ItemSubCategoryController@save');
Route::get('sub-category-get-list','Finance\Item\ItemSubCategoryController@get_sub_category_list');
Route::get('sub-category-get','Finance\Item\ItemSubCategoryController@get');
Route::get('sub-category-change-status','Finance\Item\ItemSubCategoryController@change_status');
