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

Route::prefix('user')->group(function () {
    Route::get('/set-organization', 'UserController@setOrganization');
    Route::post('/set-organization', 'UserController@setUserOrganization');

    Route::get('/', 'UserController@index');
    Route::get('/new', 'UserController@newUsers');
    Route::get('/create', 'UserController@create');
    Route::post('/create', 'UserController@store');
    Route::post('/bulk-update', 'UserController@bulkUpdate');
    Route::post('/bulk-approve', 'UserController@bulkApprove');
    Route::get('/edit/{user_id}', 'UserController@edit');
    Route::post('/edit/{user_id}', 'UserController@update');
    Route::get('/delete/{user_id}', 'UserController@destroy');
    Route::get('cities/{district_id}', 'UserController@cities');
    Route::get('districts/{state_id}', 'UserController@districts');
    Route::get('remove-image/{user_image}', 'UserController@removeImage');
    Route::get('/import', 'UserController@import');
    Route::post('/import', 'UserController@importBuyer');
    Route::get('/activity-logs', 'UserController@activityLogs');
    Route::get('/logs/{user_id}', 'UserController@logs');
    Route::post('/update-user-password', 'UserController@updateUserPassword');

    Route::get('/detail/{user_id}', 'UserController@show');
    Route::get('/address/{user_id}', 'UserController@address');
    Route::get('/address-details/{address_id}', 'UserController@addressDetails');
    Route::post('/address/{user_id}', 'UserController@addressUpdate');
    Route::get('/address/remove/{address_id}', 'UserController@removeAddress');
    Route::post('/check-user', 'UserController@checkUser');

    Route::prefix('staff')->group(function () {
        Route::get('/', 'UserController@staffList');
        Route::post('/', 'UserController@staffList');
        Route::get('/staffListOld', 'UserController@staffListOld');
        Route::get('/create-staff', 'UserController@createStaff');
        Route::post('/create-staff', 'UserController@storeStaff');
        Route::get('/edit-staff/{user_id}', 'UserController@editStaff');
        Route::post('/edit-staff/{user_id}', 'UserController@updateStaff');
        Route::get('/staff-detail/{user_id}', 'UserController@showStaff');
        Route::get('/delete-staff/{user_id}', 'UserController@destroyStaff');
        Route::post('/staff-bulk-update', 'UserController@staffBulkUpdate');
    });

    Route::get('/organization-brand', 'UserController@getBrandByOrganization');
});
Route::get('/profile', 'UserController@profile');
Route::post('/profile', 'UserController@updateProfile');
Route::get('/profile/notification', 'UserController@notification');
Route::get('/profile/activity', 'UserController@activity');
Route::get('/profile/setting', 'UserController@setting');
Route::post('/profile/setting', 'UserController@updatePassword');
Route::get('/notification', 'UserController@notifications');
Route::get('/get-notification-list', 'UserController@getNotificationList');
Route::get('/notification-mark-as-read', 'UserController@notificationMarkedAsRead');

Route::get('/profile/address', 'UserController@profileAddress');
Route::post('/profile/address', 'UserController@updateProfileAddress');

Route::group(['prefix' => 'broadcast'], function () {
    Route::get('/', 'BroadcastController@index');
    Route::post('/add', 'BroadcastController@store');
    Route::get('/table', 'BroadcastController@table');
    Route::get('/resend/{id}', 'BroadcastController@resend');
    Route::get('/delete', 'BroadcastController@destroy');
    //Route::get('/map', 'MappingController@map');
});

Route::middleware(['web', 'auth'])->prefix('human-resource')->group(function () {
    Route::group(['prefix' => 'leaves'], function () {
        Route::get('/', 'LeaveController@index');
        Route::get('/get-leaves-list', 'LeaveController@getLeavesList');
        Route::get('/view-leave-info/{id}', 'LeaveController@viewLeaveInfo');
    });

    Route::group(['prefix' => 'resignations'], function () {
        Route::get('/', 'ResignationController@index');
        Route::get('/get-resignation-list', 'ResignationController@getResignationList');
        Route::get('/view-resignation/{id}', 'ResignationController@viewResignation');
        Route::get('/reject-resignation', 'ResignationController@rejectResignation');
        Route::get('/accept-resignation', 'ResignationController@acceptResignation');
    });
});

Route::middleware(['web', 'auth'])->prefix('documents')->group(function () {
    Route::get('/', 'DocumentController@index');
    Route::post('/add-folder', 'DocumentController@createFolder');
    Route::post('/rename-folder', 'DocumentController@renameFolder');
    Route::get('/folder/{id}/', 'DocumentController@viewDocuments');
    Route::get('/delete-folder', 'DocumentController@deleteFolder');
    Route::post('/uploads-documents', 'DocumentController@uploadDocumentInFolder');
    Route::get('/delete-document', 'DocumentController@deleteDocuments');
    Route::get('/view-folder-permissions/{id}/', 'DocumentController@viewFolderPermissions');
    Route::post('/update-folder-permissions', 'DocumentController@updateFolderPermissions');
    Route::get('/get-folder-permissions-by-role', 'DocumentController@getFolderPermissionByRoleId');
});
