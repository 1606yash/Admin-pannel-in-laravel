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

Route::group(['prefix' => 'v1/role'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('all-roles', 'Api\V1\RoleController@allRoles');
        Route::get('get-permissions-by-role-id/{role_id?}', 'Api\V1\PermissionController@getPermissionsByRoleId');
    });
});
