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

Route::middleware(['web', 'auth'])->prefix('task')->group(function () {
    Route::get('/', 'TaskController@index');
    Route::get('/create-task', 'TaskController@create');
    Route::post('/store-task', 'TaskController@store');
    Route::post('/cancel-task', 'TaskController@cancelTask');
    Route::post('/assign-task', 'TaskController@assignTask');
    Route::get('/get-assigned-user', 'TaskController@getDetailsOfAssignedUser');
    Route::get('/get-task-details/{id}', 'TaskController@viewTask');
});
