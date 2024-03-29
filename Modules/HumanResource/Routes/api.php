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

// Route::middleware('auth:api')->get('/humanresource', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1/hr'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('get-salary-slip', 'Api\V1\SalaryController@getSalarySlip');
    });
});
