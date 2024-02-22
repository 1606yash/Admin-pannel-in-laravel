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

Route::middleware(['web', 'auth'])->prefix('master')->group(function () {
	Route::group(['prefix' => 'state'], function () {
		Route::get('/', 'MasterController@stateList');
		Route::post('/add', 'MasterController@storeState');
		Route::get('/get-state', 'MasterController@getState');
		Route::get('/delete', 'MasterController@destroyState');
	});
	Route::group(['prefix' => 'district'], function () {
		Route::get('/', 'MasterController@districtList');
		Route::post('/add', 'MasterController@storeDistrict');
		Route::get('/get-district', 'MasterController@getDistrict');
		Route::get('/delete', 'MasterController@destroyDistrict');
	});
	Route::group(['prefix' => 'holidays'], function () {
		Route::get('/', 'HolidayController@index');
		Route::get('/get-holiday-list', 'HolidayController@index');
		Route::get('/get-holiday-info', 'HolidayController@getHolidayInfo');
		Route::post('/update-holiday-info', 'HolidayController@updateHolidayInfo');
		Route::get('/delete-holiday', 'HolidayController@deleteHoliday');

		Route::post('/add', 'HolidayController@addHoliday');
		Route::get('/delete', 'HolidayController@deleteBulkHoliday');
		Route::get('/export-format-for-bulk-holiday-upload', 'HolidayController@exportFormat');
	});
});
