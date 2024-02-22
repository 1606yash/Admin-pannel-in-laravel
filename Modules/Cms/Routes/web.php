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

Route::prefix('cms')->group(function() {
    Route::get('/', 'CmsController@index');
    Route::group(['prefix' => 'banners'], function () {
    	Route::get('/', 'BannersController@index');
    	Route::get('/create', 'BannersController@create');
    	Route::post('/create', 'BannersController@store');
    	Route::get('/edit/{id}', 'BannersController@edit');
    	Route::post('/edit/{id}', 'BannersController@update');
    	Route::get('/delete/{id}', 'BannersController@destroy');
    });
    Route::group(['prefix' => 'pages'], function () {
    	Route::get('/', 'PagesController@index');
    	Route::get('/create', 'PagesController@create');
        Route::post('/create', 'PagesController@store');
        Route::get('/edit/{id}', 'PagesController@edit');
        Route::post('/edit/{id}', 'PagesController@update');
        Route::get('/delete/{id}', 'PagesController@destroy');
    });
});
