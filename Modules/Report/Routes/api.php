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

Route::group(['middleware' => 'auth:api','prefix' => 'v1/report'], function(){
    Route::get('top-products/{organization_id?}', 'API\V1\ReportController@topProducts');
    Route::get('sales-by-sales-person/{organization_id?}', 'API\V1\ReportController@salesBySalesPerson');
    Route::get('sales-by-buyers/{organization_id?}', 'API\V1\ReportController@salesByBuyers');
    Route::get('top-categories/{organization_id?}', 'API\V1\ReportController@topCategories');
    Route::get('sales-by-product-categories/{organization_id?}', 'API\V1\ReportController@salesByProductCategories');
    Route::get('billing/{organization_id?}', 'API\V1\ReportController@billing');
    Route::get('collection-report/{organization_id?}', 'API\V1\ReportController@collectionReport');
    Route::get('zero-billing-buyers/{organization_id?}', 'API\V1\ReportController@zeroBillingBuyers');
    Route::get('zero-billing-sales-person/{organization_id?}', 'API\V1\ReportController@zeroBillingSalesPerson');
    Route::get('target-achievement-buyers/{organization_id?}', 'API\V1\ReportController@targetAchievementForBuyer');
    Route::get('target-achievement-sales-person/{organization_id?}', 'API\V1\ReportController@targetchievementSetOfFieldTeam');

    Route::get('onfield-visits/{organization_id?}', 'API\V1\ReportController@onfieldVisits');
    Route::get('offfield-visits/{organization_id?}', 'API\V1\ReportController@offfieldVisits');

    Route::get('top-buyers/{organization_id?}', 'API\V1\ReportController@topBuyers');
    Route::get('top-sales-person/{organization_id?}', 'API\V1\ReportController@topSalesPerson');
    Route::get('zero-billing-items/{organization_id?}', 'API\V1\ReportController@zeroBillingItems');
    Route::get('pending-sales-order/{organization_id?}', 'API\V1\ReportController@pendingSalesOrder');

    
    Route::get('export-sales-by-sales-person', 'API\V1\ReportController@salesBySalesPersonExport');
    Route::get('export-sales-by-buyers', 'API\V1\ReportController@salesByBuyersExport');
    Route::get('export-top-10-products', 'API\V1\ReportController@topProductExport');
    Route::get('export-top-10-categories', 'API\V1\ReportController@topCategoriesExport');
    Route::get('export-sales-by-product-categories', 'API\V1\ReportController@salesByProductCategoriesExport');
    Route::get('export-zero-billing-sales-person', 'API\V1\ReportController@zeroBillingSalesPersonExport');
    Route::get('export-zero-billing-buyers', 'API\V1\ReportController@zeroBillingBuyersExport');
    Route::get('product-category-wise-sales-report', 'API\V1\ReportController@productCategoryWiseSalesReport');
    Route::get('listed-price-vs-invoiced-price-report', 'API\V1\ReportController@listedPriceVsInvoicedPriceReport');
    Route::get('stuff-role-report', 'API\V1\ReportController@stuffRoleReport');
    Route::get('ticket-size-report', 'API\V1\ReportController@ticketSizeReport');
    Route::get('billing-retailers-report', 'API\V1\ReportController@billingRetailersReport');
});