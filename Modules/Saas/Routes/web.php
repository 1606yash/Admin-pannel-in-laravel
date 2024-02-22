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

Route::prefix('saas')->group(function() {
    Route::get('/', 'SaasController@index');
	    Route::group(['prefix' => 'organization'], function () {
	        Route::get('/', 'OrganizationController@index');
	        Route::get('/add', 'OrganizationController@create');
	        Route::post('/store', 'OrganizationController@store');
	        Route::post('/update', 'OrganizationController@update');
	        Route::get('/edit/{id}', 'OrganizationController@edit');
	        Route::get('/details/{id}', 'OrganizationController@show');
	        Route::get('/module', 'OrganizationController@module');
	        Route::get('/industries', 'OrganizationController@industryMaster');
	        Route::get('/industry/get', 'OrganizationController@getIndustryMaster');
	        Route::post('/industry/add', 'OrganizationController@addIndustry');
	        Route::post('/industry/mass-update', 'OrganizationController@massUpdate');
	        Route::get('/industry/delete', 'OrganizationController@destroyIndustry');

	        Route::get('/segments', 'OrganizationController@segmentMaster');
	        Route::get('/segment/get', 'OrganizationController@getSegmentMaster');
	        Route::post('/segment/add', 'OrganizationController@addSegment');
	        Route::post('/segment/mass-update', 'OrganizationController@massUpdateSegment');
	        Route::get('/segment/delete', 'OrganizationController@destroySegment');

	        Route::get('/ecommerce/products', 'OrganizationController@product');
	        Route::get('/ecommerce/products/get', 'OrganizationController@getProduct');
			Route::get('/ecommerce/products/add', 'OrganizationController@addProduct');
			Route::post('/ecommerce/products/create', 'OrganizationController@createProduct');
			Route::get('/ecommerce/products/edit/{id?}', 'OrganizationController@editProduct');
			Route::post('/ecommerce/products/edit/{id?}', 'OrganizationController@updateProduct');
			Route::get('/ecommerce/products/delete', 'OrganizationController@destroyProduct');
			Route::post('/ecommerce/products/mass-update', 'OrganizationController@massUpdateProduct');
			Route::get('/ecommerce/products/manufacturer/get-models/{id}', 'OrganizationController@getManuModels');

	        Route::get('/ecommerce/brands', 'OrganizationController@brands');
	        Route::post('/ecommerce/brands/add', 'OrganizationController@addBrand');
	        Route::get('/ecommerce/brands/get', 'OrganizationController@getBrand');
	        Route::post('/ecommerce/brands/mass-update', 'OrganizationController@massUpdateBrand');
	        Route::get('/ecommerce/brands/delete', 'OrganizationController@destroyBrand');

	        Route::get('/ecommerce/categories', 'OrganizationController@categories');
			Route::get('/ecommerce/categories/get', 'OrganizationController@getCategory');
			Route::post('/ecommerce/categories/add', 'OrganizationController@addCategory');
			Route::get('/ecommerce/categories/delete', 'OrganizationController@destroyCategory');
			Route::post('/ecommerce/categories/mass-update', 'OrganizationController@massUpdateCategory');

			Route::get('/ecommerce/models', 'OrganizationController@models');
			Route::get('/ecommerce/models/get', 'OrganizationController@getModel');
			Route::post('/ecommerce/models/add', 'OrganizationController@addModel');
			Route::get('/ecommerce/models/delete', 'OrganizationController@destroyModel');
			Route::post('/ecommerce/models/mass-update', 'OrganizationController@massUpdateModel');

			Route::get('/ecommerce/manufacturer', 'OrganizationController@manufacturer');
			Route::get('/ecommerce/manufacturer/get', 'OrganizationController@getManufacturer');
			Route::post('/ecommerce/manufacturer/add', 'OrganizationController@addManufacturer');
			Route::get('/ecommerce/manufacturer/delete', 'OrganizationController@destroyManufacturer');
			Route::post('/ecommerce/manufacturer/mass-update', 'OrganizationController@massUpdateManufacturer');

	        Route::get('/modules', 'OrganizationController@modules');
	        Route::get('/modules/detail', 'OrganizationController@detail');
	        Route::get('/get-module', 'OrganizationController@getModule');
	        Route::post('/update-module', 'OrganizationController@updateModule');
	        
	        
	        Route::get('/settings', 'OrganizationController@settings');
	        Route::get('/get-setting', 'OrganizationController@getSetting');
	        Route::post('/settings/add', 'OrganizationController@addSetting');
	        Route::get('/settings/delete', 'OrganizationController@destroySettings');
	        Route::get('/cities/{id}', 'OrganizationController@getCitiesByState');
	    });
});
