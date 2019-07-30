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
    Route::post('suppliers/load_currency','Org\SupplierController@load_currency');

    Route::get('supplierslist/loadsuppliers' , 'Org\SupplierController@loadSuppliers');
    Route::apiResource('supplierslist','Org\SupplierController');

    Route::get('uom/validate' , 'Org\UomController@validate_data');
    Route::apiResource('uom','Org\UomController');

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

    Route::get('stores/validate' , 'Store\StoreController@validate_data');
    Route::apiResource('stores','Store\StoreController');

    Route::get('product-specification/validate' , 'Org\ProductSpecificationController@validate_data');
    Route::apiResource('product-specifications','Org\ProductSpecificationController');

    Route::get('silhouette-classification/validate' , 'Org\SilhouetteClassificationController@validate_data');
    Route::apiResource('silhouette-classification','Org\SilhouetteClassificationController');

    Route::get('silhouettes/validate' , 'Org\SilhouetteController@validate_data');
    Route::apiResource('silhouettes','Org\SilhouetteController');

    Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
    Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

    Route::get('features/validate' , 'Org\FeatureController@validate_data');
    Route::apiResource('features','Org\FeatureController');
    Route::post('features/load_pro_list', 'Org\FeatureController@load_pro_list');

    Route::get('garmentoptions/validate' , 'Org\GarmentOptionsController@validate_data');
    Route::apiResource('garmentoptions','Org\GarmentOptionsController');

    Route::get('requestType/validate' , 'Org\RequestTypeController@validate_data');
    Route::apiResource('requestType','Org\RequestTypeController');
    Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
    Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

    Route::apiResource('ship-modes','Org\ShipModeController');
    Route::get('designations/validate' , 'Org\DesignationController@validate_data');
    Route::apiResource('designations','Org\DesignationController');

    Route::get('PoType/validate' , 'Org\PoTypeController@validate_data');
    Route::apiResource('PoType','Org\PoTypeController');

    Route::get('silhouette-classification/validate' , 'Org\SilhouetteClassificationController@validate_data');
    Route::apiResource('silhouette-classification','Org\SilhouetteClassificationController');

    //Route::get('features/validate' , 'Org\FeatureController@validate_data');
    //Route::apiResource('features','Org\FeatureController');

    Route::get('silhouettes/validate' , 'Org\SilhouetteController@validate_data');
    Route::apiResource('silhouettes','Org\SilhouetteController');

    Route::get('garmentoptions/validate' , 'Org\GarmentOptionsController@validate_data');
    Route::apiResource('garmentoptions','Org\GarmentOptionsController');

    Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
    Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

  Route::get('requestType/validate' , 'Org\RequestTypeController@validate_data');
  Route::apiResource('requestType','Org\RequestTypeController');
  Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
  Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

  Route::apiResource('ship-modes','Org\ShipModeController');
  Route::get('designations/validate' , 'Org\DesignationController@validate_data');
  Route::apiResource('designations','Org\DesignationController');

  Route::get('PoType/validate' , 'Org\PoTypeController@validate_data');
  Route::apiResource('PoType','Org\PoTypeController');

  Route::get('silhouette-classification/validate' , 'Org\SilhouetteClassificationController@validate_data');
  Route::apiResource('silhouette-classification','Org\SilhouetteClassificationController');

  //Route::get('features/validate' , 'Org\FeatureController@validate_data');
  //Route::apiResource('features','Org\FeatureController');

  Route::apiResource('ship-modes','Org\ShipModeController');

});



//});

Route::prefix('stores/')->group(function(){
    Route::apiResource('generalpr','stores\GeneralPRController');
    Route::apiResource('generalpr_details','stores\GeneralPRDetailController');
    //Route::get('get_genpr','stores\GeneralPRController');


    Route::apiResource('get_genpr','stores\GeneralPRController');
});

Route::prefix('ie/')->group(function(){

    Route::get('smvupdates/validate' , 'IE\SMVUpdateController@validate_data');
    Route::get('smvupdates/divisions' , 'IE\SMVUpdateController@customer_divisions');
    Route::put('smvupdates/updates' , 'IE\SMVUpdateController@update');
    Route::apiResource('smvupdates','IE\SMVUpdateController');

    Route::apiResource('smvupdatehistories','IE\SMVUpdateHistoryController');
    Route::put('smvupdatehistories/updates' , 'IE\SMVUpdateHistoryController@update');


    Route::get('servicetypes/validate' , 'IE\ServiceTypeController@validate_data');
    Route::apiResource('servicetypes','IE\ServiceTypeController');

  Route::get('servicetypes/validate' , 'IE\ServiceTypeController@validate_data');
  Route::apiResource('servicetypes','IE\ServiceTypeController');
  Route::get('garment_operations/validate' , 'IE\GarmentOperationMasterController@validate_data');
  Route::apiResource('garment_operations','IE\GarmentOperationMasterController');
   Route::apiResource('styles','Merchandising\StyleCreationController');
  // Route::apiResource('bomStages','Merchandising\BOMStageController');
   Route::apiResource('componentSMVDetails','IE\ComponentSMVController');
   Route::post('componentSMVDetails/saveDataset','IE\ComponentSMVController@storeDataset');
   Route::post('componentSMVDetails/checkSMVRange' , 'IE\ComponentSMVController@check_smv_range');
    //Route::apiResource('garment_operations','IE\GarmentOperationMasterController');


});

Route::prefix('items/')->group(function(){
    Route::get('itemlists/loadItemList' , 'itemCreationController@GetItemList');
    Route::apiResource('itemlists','itemCreationController');

    Route::get('itemlist/loadItemsbycat' , 'itemCreationController@GetItemListBySubCategory');
    Route::apiResource('itemlist','itemCreationController');

    Route::get('getitem/getItemByCode' , 'itemCreationController@GetItemDetailsByCode');
    Route::apiResource('getitem','itemCreationController');
    //item cretaion duplicate validataion
    Route::get('itemCreation/validate' , 'itemCreationController@validate_data');
    Route::get('garment_operations/validate' , 'IE\GarmentOperationMasterController@validate_data');
    Route::apiResource('itemCreation','itemCreationController');


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

    Route::get('transaction/validate' , 'Finance\TransactionController@validate_data');
    Route::apiResource('transaction','Finance\TransactionController');
    //sub category duplication validate
    Route::get('subcategory/validate' , 'Finance\Item\ItemSubCategoryController@check_sub_category_code');

    Route::apiResource('finCost','Finance\Cost\FinanceCostController');

    Route::apiResource('finCostHis','Finance\Cost\FinanceCostHistoryController');
    Route::put('finCostHis/updates' , 'Finance\Cost\FinanceCostHistoryController@update');

});


Route::prefix('stores/')->group(function(){

  Route::apiResource('po-load','stores\RollPlanController');
  Route::apiResource('roll','stores\RollPlanController');
  /********edited*/
  Route::get('supplier-tolarance/validate' , 'Stores\SupplierTolaranceController@validate_data');
  Route::apiResource('supplier-tolarance','Stores\SupplierTolaranceController');
  Route::apiResource('fabricInspection','stores\FabricInspectionController');
  Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
  Route::post('transfer-location-store','Stores\TransferLocationController@storedetails');
  Route::apiResource('transfer-location','Stores\TransferLocationController');
  Route::apiResource('grn', 'Store\GrnController');
  Route::post('save-grn-bin', 'Store\GrnController@saveGrnBins');
  Route::get('load-grn-lines', 'Store\GrnController@loadAddedGrnLInes');
  Route::get('loadPoBinList','Store\StoreBinController@getBinListByLoc');
  Route::get('loadAddedBins','Store\GrnController@getAddedBins');
  Route::get('load-substores','Store\SubStoreController@getSubStoreList');
  //sub store bin validate
  Route::get('subStoreBin/validate' , 'Store\StoreBinController@validate_data');
  Route::apiResource('substore','Store\StoreBinController');
  Route::get('substore-bin-list','Store\SubStoreController@getSubStoreBinList');
  Route::get('load-bin-qty','Store\BinTransferController@loadBinQty');
  Route::get('load-added-bin-qty','Store\BinTransferController@loadAddedBinQty');
  Route::post('add-bin-qty','Store\BinTransferController@addBinTrnsfer');
  Route::apiResource('save-bin-transfer', 'Store\BinTransferController');

  Route::post('load-stock-for-mrn','Store\StockController@searchStock');
  Route::apiResource('po-load','stores\RollPlanController');
  Route::apiResource('roll','stores\RollPlanController');
    /********edited*/
  Route::get('supplier-tolarance/validate' , 'Stores\SupplierTolaranceController@validate_data');
  Route::apiResource('supplier-tolarance','Stores\SupplierTolaranceController');
  Route::apiResource('fabricInspection','stores\FabricInspectionController');
  Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
  Route::post('transfer-location-store','Stores\TransferLocationController@storedetails');
  Route::apiResource('transfer-location','Stores\TransferLocationController');
  Route::apiResource('grn', 'Store\GrnController');
  Route::post('save-grn-bin', 'Store\GrnController@saveGrnBins');
  Route::get('load-grn-lines', 'Store\GrnController@loadAddedGrnLInes');
  Route::get('loadPoBinList','Store\StoreBinController@getBinListByLoc');
  Route::get('loadAddedBins','Store\GrnController@getAddedBins');
  Route::get('load-substores','Store\SubStoreController@getSubStoreList');
  Route::get('substore-bin-list','Store\SubStoreController@getSubStoreBinList');
  Route::get('load-bin-qty','Store\BinTransferController@loadBinQty');
  Route::get('load-added-bin-qty','Store\BinTransferController@loadAddedBinQty');
  Route::post('add-bin-qty','Store\BinTransferController@addBinTrnsfer');
  Route::apiResource('save-bin-transfer', 'Store\BinTransferController');

    //Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
    //Route::post('material-transfer','Stores\MaterialTransferController@datatable_search');
  Route::apiResource('material-transfer','Stores\MaterialTransferController');
  Route::post('material-transfer-store','Stores\MaterialTransferController@storedetails');
    //Route::get('material-transfer','Stores\MaterialTransferController@getStores');
    //Route::apiResource('substore','Store\SubStoreController');
  Route::get('stock-bal-for-return-to-sup','Store\StockController@getStockForReturnToSup');

});

Route::prefix('merchandising/')->group(function(){

//  Route::get('g/validate' , 'Finance\GoodsTypeController@validate_data');
    Route::apiResource('customer-orders','Merchandising\CustomerOrderController');

    Route::post('customer-order-details/split-delivery','Merchandising\CustomerOrderDetailsController@split_delivery');
    Route::post('customer-order-details/merge','Merchandising\CustomerOrderDetailsController@merge');
    Route::get('customer-order-details/revisions','Merchandising\CustomerOrderDetailsController@revisions');
    Route::get('customer-order-details/origins','Merchandising\CustomerOrderDetailsController@origins');
    Route::apiResource('customer-order-details','Merchandising\CustomerOrderDetailsController');

    Route::apiResource('customer-order-sizes','Merchandising\CustomerOrderSizeController');
    Route::apiResource('customer-order-types','Merchandising\CustomerOrderTypeController');
    Route::apiResource('get-style','Merchandising\StyleCreationController');
    Route::apiResource('tna-master','Merchandising\TnaMasterController');
    Route::get('color-options/validate' , 'Merchandising\ColorOptionController@validate_data');
    Route::apiResource('color-options','Merchandising\ColorOptionController');

    Route::get('position/validate' , 'Merchandising\PositionController@validate_data');
    Route::apiResource('position','Merchandising\PositionController');

    Route::get('style/validate' , 'Merchandising\StyleCreationController@validate_data');
    Route::apiResource('style','Merchandising\StyleCreationController');
    Route::post('pro_listload', 'Merchandising\StyleCreationController@pro_listload');


    Route::get('rounds/validate' , 'Merchandising\RoundController@validate_data');
    Route::apiResource('rounds','Merchandising\RoundController');

    Route::get('bomstages/validate' , 'Merchandising\BOMStageController@validate_data');
    Route::apiResource('bomstages','Merchandising\BOMStageController');

    Route::get('cut-direction/validate' , 'Merchandising\CutDirectionController@validate_data');
    Route::apiResource('cut-direction','Merchandising\CutDirectionController');

    Route::get('matsize/validate' , 'Merchandising\MaterialSizeController@validate_data');
    Route::get('matsize/subcat', 'Merchandising\MaterialSizeController@get_sub_cat');
    Route::apiResource('matsize','Merchandising\MaterialSizeController');

    Route::get('loadPoLineData','Merchandising\PurchaseOrder@loadPoLineData');
    Route::get('loadPoSCList','Merchandising\PurchaseOrder@getPoSCList');
    Route::get('loadCostingData','Merchandising\PurchaseOrder@getCostingData');
    Route::apiResource('purchase-order-data','Merchandising\PurchaseOrder');

    Route::get('loadCostingDataForCombine','Merchandising\Costing\CostingController@getCostingDataForCombine');
    Route::get('costing/validate' , 'Merchandising\Costing\CostingController@validate_data');
    Route::post('costing/finish-good/copy','Merchandising\Costing\CostingController@copy_finish_good');
    Route::post('costing/finish-good/delete','Merchandising\Costing\CostingController@delete_finish_good');

    Route::post('costing/copy','Merchandising\Costing\CostingController@copy');
    Route::apiResource('costing','Merchandising\Costing\CostingController');

    Route::post('costing-finish-good-items-save','Merchandising\Costing\CostingFinishGoodItemController@save_items');
    Route::post('costing-finish-good-items-copy','Merchandising\Costing\CostingFinishGoodItemController@copy');
    Route::apiResource('costing-finish-good-items','Merchandising\Costing\CostingFinishGoodItemController');

  //  Route::get('bulk/validate' , 'Merchandising\BulkCosting\BulkDetailsController@validate_data');
  //  Route::apiResource('bulk','Merchandising\BulkCosting\BulkDetailsController');


    //Route::get('loadSoList','Merchandising\BulkCosting\BulkCostingController@getSOByStyle');
    Route::apiResource('so-combine', 'Merchandising\CombineSOController');

    Route::apiResource('po-general','Merchandising\PurchaseOrderGeneralController');
    Route::apiResource('po-general-details','Merchandising\PurchaseOrderGeneralDetailsController');

    Route::apiResource('po-manual','Merchandising\PurchaseOrderManualController');
    Route::apiResource('po-manual-details','Merchandising\PurchaseOrderManualDetailsController');

    //Route::get('bulk-costing/validate' , 'Merchandising\BulkCosting\BulkCostingController@validate_data');
    //Route::apiResource('bulk-costing','Merchandising\BulkCosting\BulkCostingController');


  Route::apiResource('po-load','stores\RollPlanController');
  Route::apiResource('roll','stores\RollPlanController');
  /********edited*/
  Route::get('supplier-tolarance/validate' , 'Stores\SupplierTolaranceController@validate_data');
  Route::apiResource('supplier-tolarance','Stores\SupplierTolaranceController');
  Route::apiResource('fabricInspection','stores\FabricInspectionController');
  Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
  Route::post('transfer-location-store','Stores\TransferLocationController@storedetails');
  Route::apiResource('transfer-location','Stores\TransferLocationController');
  Route::apiResource('grn', 'Store\GrnController');
  Route::post('save-grn-bin', 'Store\GrnController@saveGrnBins');
  Route::get('load-grn-lines', 'Store\GrnController@loadAddedGrnLInes');
  Route::get('loadPoBinList','Store\StoreBinController@getBinListByLoc');
  Route::get('loadAddedBins','Store\GrnController@getAddedBins');
  Route::get('load-substores','Store\SubStoreController@getSubStoreList');
  Route::get('substore-bin-list','Store\SubStoreController@getSubStoreBinList');
  Route::get('load-bin-qty','Store\BinTransferController@loadBinQty');
  Route::get('load-added-bin-qty','Store\BinTransferController@loadAddedBinQty');
  Route::post('add-bin-qty','Store\BinTransferController@addBinTrnsfer');
  Route::apiResource('save-bin-transfer', 'Store\BinTransferController');

    Route::get('bulk/validate' , 'Merchandising\BulkCosting\BulkDetailsController@validate_data');
    Route::apiResource('bulk','Merchandising\BulkCosting\BulkDetailsController');
    Route::post('po-manual-details/load_bom_Details','Merchandising\PurchaseOrderManualController@load_bom_Details');
    Route::post('po-manual-details/load_reqline','Merchandising\PurchaseOrderManualController@load_reqline');
    Route::post('po-manual-details/load_reqline_2','Merchandising\PurchaseOrderManualDetailsController@load_reqline_2');
    Route::post('po-manual-details/load_por_line','Merchandising\PurchaseOrderManualDetailsController@load_por_line');
    Route::post('po-manual-details/merge_save','Merchandising\PurchaseOrderManualController@merge_save');

    Route::post('po-manual-details/save_line_details','Merchandising\PurchaseOrderManualDetailsController@save_line_details');
    Route::post('po-manual-details/update_line_details','Merchandising\PurchaseOrderManualDetailsController@update_line_details');
    Route::post('po-manual/revision_header' , 'Merchandising\PurchaseOrderManualDetailsController@load_po_revision_header');
    Route::post('po-manual/prl_header_load' , 'Merchandising\PurchaseOrderManualDetailsController@prl_header_load');
    Route::post('po-delivery-split' , 'Merchandising\PurchaseOrderManualDetailsController@po_delivery_split');
    Route::get('po-delivery-split-load' , 'Merchandising\PurchaseOrderManualDetailsController@po_delivery_split_load');


    Route::post('po-manual-details/save_line_details_revision','Merchandising\PurchaseOrderManualDetailsController@save_line_details_revision');
    //Route::get('bulk-costing-header' , 'Merchandising\BulkCosting\BulkCostingController');
    Route::apiResource('bulk-cost-listing','Merchandising\BulkCosting\BulkCostingController');
    Route::apiResource('bulk-cost-header','Merchandising\BulkCosting\BulkCostingController');

    Route::get('bom/custorders','Merchandising\BomController@getCustOrders');
    Route::get('bom/custorderQty','Merchandising\BomController@getCustomerOrderQty');
    Route::get('bom/assigncustorders','Merchandising\BomController@getAssignCustOrders');

    Route::get('bom/rmdetails','Merchandising\BomController@getCostingRMDetails');
    Route::get('bom/bomlist','Merchandising\BomController@ListBOMS');
    Route::get('bom/bominfolisting','Merchandising\BomController@getBOMDetails');
    Route::get('bom/bomorderqty','Merchandising\BomController@getBOMOrderQty');
    Route::get('bom/sizewise','Merchandising\BomController@getSizeWiseDetails');
    Route::get('bom/colorcombolist','Merchandising\BomController@getColorCombo');
    Route::get('bom/getratio','Merchandising\BomController@getMatRatio');

    Route::post('bom/savebomheader','Merchandising\BomController@saveBOMHeader');
    Route::post('bom/savebomdetail','Merchandising\BomController@saveBOMDetails');
    Route::post('bom/savesoallocation','Merchandising\BomController@saveSOAllocation');
    Route::post('bom/savesmaterialratio','Merchandising\BomController@saveMaterialRatio');

    Route::post('items/check_and_generate_item_description','Merchandising\Item\ItemController@check_and_generate_item_description');
    Route::apiResource('items','Merchandising\Item\ItemController');

    Route::apiResource('item-categories','Merchandising\Item\CategoryController');
    Route::apiResource('item-sub-categories','Merchandising\Item\SubCategoryController');
    Route::apiResource('item-content-types','Merchandising\Item\ContentTypeController');
    Route::apiResource('item-compositions','Merchandising\Item\CompositionController');
    Route::get('item-properties/validate','Merchandising\Item\ItemPropertyController@validate_data');
    Route::apiResource('item-properties','Merchandising\Item\ItemPropertyController');
    Route::apiResource('item-property-values','Merchandising\Item\ItemPropertyValueController');

    Route::post('load_un_assign_list', 'Merchandising\Item\ItemPropertyController@load_un_assign_list');
    Route::post('load_un_assign_list2', 'Merchandising\Item\ItemPropertyController@load_un_assign_list2');
    Route::post('save_assign', 'Merchandising\Item\ItemPropertyController@save_assign');
    Route::post('final_save_assign', 'Merchandising\Item\ItemPropertyController@final_save_assign');
    Route::post('save_pro_name', 'Merchandising\Item\ItemPropertyController@save_pro_name');
    Route::post('remove_assign', 'Merchandising\Item\ItemPropertyController@remove_assign');
    Route::post('remove_unassign', 'Merchandising\Item\ItemPropertyController@remove_unassign');


    Route::apiResource('pro-silhouette','Merchandising\ProductSilhouetteController');
    Route::post('save_product_feature','Merchandising\ProductFeatureController@save_product_feature');
    Route::post('pro_listload_edit', 'Merchandising\ProductFeatureController@pro_listload_edit');
    Route::apiResource('product_feature','Merchandising\ProductFeatureController');
    Route::post('update_product_feature','Merchandising\ProductFeatureController@update_product_feature');
    Route::post('save_line_fe', 'Merchandising\ProductFeatureController@save_line_fe');


});

Route::prefix('admin/')->group(function(){

    Route::get('users/roles','Admin\UserController@roles');
    Route::post('users/roles','Admin\UserController@save_roles');
    Route::get('users/locations','Admin\UserController@locations');
    Route::post('users/locations','Admin\UserController@save_locations');
    Route::get('users/user-assigned-locations','Admin\UserController@user_assigned_locations');
    Route::apiResource('users','Admin\UserController');


    Route::get('permission/validate' , 'Admin\PermissionController@validate_data');
    Route::apiResource('permission','Admin\PermissionController');

    Route::get('roles/validate' , 'Admin\RoleController@validate_data');
    Route::post('roles/change-role-permission','Admin\RoleController@change_role_permission');
    Route::apiResource('roles','Admin\RoleController');

    Route::apiResource('permission-categories','Admin\PermissionCategoryController');
    Route::apiResource('permissions','Admin\PermissionController');

  Route::get('approval-stages/validate' , 'Admin\ApprovalStageController@validate_data');
  Route::apiResource('approval-stages','Admin\ApprovalStageController');

  Route::apiResource('process-approvals','Admin\ProcessApprovalController');


});

Route::prefix('store/')->group(function(){

    Route::auth();

    Route::get('stores/validate' , 'Store\StoreController@validate_data');
    Route::apiResource('stores','Store\StoreController');

    Route::get('storebin/validate' , 'Store\StoreBinController@validate_data');
    Route::apiResource('storebin','Store\StoreBinController');

    Route::get('substore/validate' , 'Store\SubStoreController@validate_data');
    Route::apiResource('substore','Store\SubStoreController');

    Route::get('mat-trans-in/validate' , 'Store\MaterialTransferInController@validate_data');
    Route::apiResource('mat-trans-in','Store\MaterialTransferInController');


    Route::apiResource('fabricInspection','Store\FabricInspectionController');


    Route::get('bin-config/validate' , 'Store\BinConfigController@validate_data');
    Route::apiResource('bin-config','Store\BinConfigController');

    Route::apiResource('mrn','Store\MrnController');
    Route::apiResource('issue','Store\IssueController');
    Route::apiResource('return-to-stores','Store\ReturnToStoresController');
});


Route::prefix('core/')->group(function(){

    Route::apiResource('status','Core\StatusController');

});

Route::prefix('manufacturing/')->group(function(){

    Route::apiResource('prod-order','Manufacturing\ProdOrderController');

});


Route::prefix('app/')->group(function(){


  Route::get('users/roles','Admin\UserController@roles');
  Route::post('users/roles','Admin\UserController@save_roles');
  Route::get('users/locations','Admin\UserController@locations');
  Route::post('users/locations','Admin\UserController@save_locations');
  Route::get('users/user-assigned-locations','Admin\UserController@user_assigned_locations');
  Route::apiResource('users','Admin\UserController');


    Route::GET('menus','App\MenuController@index');
    Route::POST('search','App\SearchController@index');
    Route::POST('required-permissions','App\PermissionController@get_required_permissions');
    Route::apiResource('bookmarks', 'App\BookmarkController')->only(['index', 'store']);

});

//Route::group(['middleware' => ['jwt.auth']], function() {


  Route::GET('menus','App\MenuController@index');
  Route::POST('search','App\SearchController@index');
  Route::POST('required-permissions','App\PermissionController@get_required_permissions');
  Route::apiResource('bookmarks', 'App\BookmarkController')->only(['index', 'store']);


Route::GET('/sources','Test\SourceController@index');
Route::GET('/getCustomer','Org\CustomerController@loadCustomer');
Route::GET('/getProductCategory','Merchandising\ProductCategoryController@loadProductCategory');
Route::GET('/getProductType','Merchandising\ProductTypeController@loadProductType');
Route::GET('/getProductFeature','Merchandising\ProductFeatureController@loadProductFeature');
Route::GET('/getProductSilhouette','Merchandising\ProductSilhouetteController@loadProductSilhouette');

Route::POST('/style-creation.save','Merchandising\StyleCreationController@saveStyleCreation');
Route::get('/loadstyles','Merchandising\StyleCreationController@loadStyles');
Route::get('/loadStyleDetails','Merchandising\StyleCreationController@GetStyleDetails');

Route::get('/seasonlist' , 'Org\SeasonController@GetSeasonsList');
Route::apiResource('seasons','Org\SeasonController');

Route::post('flashcosting/savecostingheader', 'Merchandising\Costing\Flash\FlashController@saveCostingHeader');
Route::post('flashcosting/savecostingdetails', 'Merchandising\Costing\Flash\FlashController@saveCostingDetails');

Route::post('flashcosting/confirmcosting', 'Merchandising\Costing\Flash\FlashController@confirmCostSheet');
Route::post('flashcosting/revisecosting', 'Merchandising\Costing\Flash\FlashController@reviseCostSheet');
Route::post('flashcosting/setinactive', 'Merchandising\Costing\Flash\FlashController@setItemInactive');

Route::get('flashcosting/listcosting', 'Merchandising\Costing\Flash\FlashController@ListingCostings');
Route::get('flashcosting/listcostingheader', 'Merchandising\Costing\Flash\FlashController@getCostingHeader');
Route::get('flashcosting/listcostinglines', 'Merchandising\Costing\Flash\FlashController@getCostingLines');
Route::get('flashcosting/getcostitems', 'Merchandising\Costing\Flash\FlashController@getCostingItems');

/*Route::post('/sources','Test\SourceController@index');

  Route::get('logout', 'AuthController@logout');
  Route::get('test', function(){
      return response()->json(['foo'=>'bar']);
  });*/
//});

  Route::GET('/sources','Test\SourceController@index');
  Route::GET('/getCustomer','Org\CustomerController@loadCustomer');
  Route::GET('/getProductCategory','Merchandising\ProductCategoryController@loadProductCategory');
  Route::GET('/getProductType','Merchandising\ProductTypeController@loadProductType');
  Route::GET('/getProductFeature','Merchandising\ProductFeatureController@loadProductFeature');
  Route::GET('/getProductSilhouette','Merchandising\ProductSilhouetteController@loadProductSilhouette');
  //Route::GET('/getDivision','Org\CustomerController@loadCustomerDivision');

Route::GET('/getDivision','Org\CustomerController@loadCustomerDivision');
