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

Route::group(['prefix' => 'v1/task'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('get-user-tasks', 'Api\V1\TaskController@getUserTasks');
        Route::post('add-task', 'Api\V1\TaskController@addTask');
        Route::post('update-task-status', 'Api\V1\TaskController@updateTaskStatus');
    });
});