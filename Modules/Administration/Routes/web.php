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

Route::prefix('administration')->group(function() {
    Route::get('/', 'AdministrationController@index');

	Route::group(['prefix' => 'menu'], function () {
        Route::post('update-order', 'MenuController@updateOrder');
        Route::get('/get-menu', 'MenuController@getMenu');
        Route::get('/delete-menu/{id}', 'MenuController@destroy');
        Route::post('/add', 'MenuController@store');
        Route::get('/jsons', 'MenuController@jsonFile');
        Route::get('/updateJson', 'MenuController@updateJson');
    	Route::get('/{menu_type?}', 'MenuController@index');
    });
    Route::group(['prefix' => 'notification'], function () {
        Route::get('/templates', 'NotificationController@index');
        Route::get('/templates/edit/{id}', 'NotificationController@create');
        Route::post('/templates/update', 'NotificationController@update');
    });

	Route::group(['prefix' => 'contactus'], function () {
    	Route::get('/', 'ContactusController@index');
        Route::POST('/details', 'ContactusController@show');
        //Route::get('/edit/{id}', 'NotificationController@edit');
    });

    Route::get('/organization', 'OrganizationController@index');
    Route::get('/organization-create', 'OrganizationController@organizationCreate');
    Route::post('/organization-create', 'OrganizationController@store');
    Route::get('/organization-edit/{id}', 'OrganizationController@edit');
    Route::post('/organization-edit/{id}', 'OrganizationController@update');
    Route::post('/organization/bulk-update', 'OrganizationController@bulkUpdate');
    Route::get('/organization-delete/{id}', 'OrganizationController@destroy');
    Route::get('/organization/logs/{id}', 'OrganizationController@logs');

    Route::group(['prefix' => 'settings'], function () {
    	Route::get('/', 'SettingsController@index');
        Route::post('/', 'SettingsController@store');
        Route::post('home-settings', 'SettingsController@updateHomeSettings');
        Route::post('integration', 'SettingsController@updateIntegrationSettings');
        //Route::get('/edit/{id}', 'NotificationController@edit');
    });
    Route::group(['prefix' => 'labels'], function () {
        Route::get('/', 'LanguageController@index');
        Route::post('/', 'LanguageController@store');
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', 'RoleController@index');
        Route::get('/get-role', 'RoleController@getRole');
        Route::get('/delete-role/{id}', 'RoleController@destroy');
        Route::post('/add', 'RoleController@store');
        //Route::get('/map', 'MappingController@map');
    });

    Route::group(['prefix' => 'permissions'], function () {

        Route::get('/{role_id?}', 'PermissionsController@index');
        Route::get('add', 'PermissionsController@create');
        Route::post('add', 'PermissionsController@store');
        Route::get('/{role_id}', 'PermissionsController@show');
    });

    Route::group(['prefix' => 'mapping'], function () {
        Route::get('/', 'MappingController@index');
        Route::get('/map-buyers/{sp_id}', 'MappingController@mapBuyers');
        Route::post('/map-buyers/{sp_id}', 'MappingController@store');
        Route::post('/unmap-buyers/{sp_id}', 'MappingController@unmapBuyers');
        Route::get('/bulk-map', 'MappingController@bulkmapping');
        Route::get('/check-mapping', 'MappingController@checkmapping');
    });
});
