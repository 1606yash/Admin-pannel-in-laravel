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
Route::group(['prefix' => 'v1/expense'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('get-user-expenses', 'Api\V1\ExpenseController@getUserExpenses');
        Route::post('add-expense', 'Api\V1\ExpenseController@addExpense');
    });
});