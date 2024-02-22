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
*/Route::middleware(['web', 'auth'])->prefix('role')->group(function () {
    Route::get('/', 'RoleController@index');
    Route::get('/get-role-list', 'RoleController@roleList');
    Route::post('/add', 'RoleController@storeRole');
    Route::get('/get-role', 'RoleController@getRole');
    Route::get('/delete', 'RoleController@destroyRole');
    Route::get('/manage-permission/{role_id}/', 'RoleController@managePermission');
    Route::post('/update-permission', 'RoleController@storePermission');

});

// Route::prefix('role')->group(function() {
//     Route::get('/', 'RoleController@roleList');
//     Route::post('/add', 'RoleController@storeRole');
//     Route::get('/get-role', 'RoleController@getRole');
//     Route::get('/delete', 'RoleController@destroyRole');
// });
