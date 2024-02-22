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

Route::group(['prefix' => 'v1/master'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('all-master', 'Api\V1\MasterController@allMaster');
        Route::post('get-holidays', 'Api\V1\HolidayController@getHolidays');
    });
});