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

Route::prefix('location')->group(function() {
	Route::group(['prefix' => 'country'], function () {
	    Route::get('/', 'LocationController@index');
	    Route::post('/add', 'LocationController@store');
	    Route::get('/get-country', 'LocationController@getCountry');
	    Route::get('/delete', 'LocationController@destroy');
    });

    Route::group(['prefix' => 'state'], function () {
	    Route::get('/', 'LocationController@stateList');
	    Route::post('/add', 'LocationController@storeState');
	    Route::get('/get-state', 'LocationController@getState');
	    Route::get('/delete', 'LocationController@destroyState');
    });

    Route::group(['prefix' => 'city'], function () {
	    Route::get('/', 'LocationController@cityList');
	    Route::post('/add', 'LocationController@storeCity');
	    Route::get('/get-city', 'LocationController@getCity');
	    Route::get('/get-states', 'LocationController@getStates');
	    Route::get('/delete', 'LocationController@destroyCity');
	    Route::post('/mass-update', 'LocationController@massUpdate');
	    Route::post('/import', 'LocationController@import');
    });
    
    
});
