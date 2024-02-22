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

Route::middleware(['web', 'auth'])->prefix('human-resource')->group(function () {
    Route::group(['prefix' => 'employees'], function () {
        Route::get('/super-admin', 'HumanResourceController@index');
        Route::get('/edit-user/{user_id}', 'HumanResourceController@editUser');
        Route::get('/{role}/view-user/{user_id}', 'HumanResourceController@viewUser');
        Route::post('/update-user', 'HumanResourceController@updateUserDetails');
        Route::get('/update-user-status', 'HumanResourceController@updateStatus');
        Route::get('/approve-user', 'HumanResourceController@approveUserAccount');
        Route::get('/reject-user', 'HumanResourceController@rejectUserRequest');
        Route::get('/get-ambulance-by-district-id', 'HumanResourceController@getAmbulanceByDistrictId');
        Route::get('/get-service-area-by-district-id', 'HumanResourceController@getServiceAreaByDistrictId');
        Route::get('/get-payslip', 'HumanResourceController@getPayslipByMonthYearId');
        Route::post('/add-payslip-by-month-id', 'HumanResourceController@addSalarySlipByMonthYearId');
        Route::get('/delete-payslip-by-month-id', 'HumanResourceController@deleteSalarySlipByMonthYear');
        Route::post('/update-salary-slip', 'HumanResourceController@updateSalarySlipDetails');
        Route::post('/download-payslip', 'HumanResourceController@downloadSalarySlip');
        Route::get('/get-attendance-by-user-id/{month_id}/{year_id}/{user_id}', 'HumanResourceController@getAttendanceByMonthYearId');
        Route::get('/get-attendance-info-by-date', 'HumanResourceController@getAttendanceInfoByDate');
        Route::post('/update-attendance-by-user', 'HumanResourceController@updateAttendanceInfoByDate');
        Route::get('/get-leave-info-by-date', 'HumanResourceController@getLeaveDetailsByDate');
        Route::get('/get-task-info-by-date', 'HumanResourceController@getTaskInfoByDate');
        Route::get('/approve-leave', 'HumanResourceController@approveLeave');
        Route::get('/reject-leave', 'HumanResourceController@rejectLeave');
        Route::get('/{role}/add-user', 'HumanResourceController@addUser');
        Route::post('/add-user', 'HumanResourceController@storeUser');
        Route::get('/get-shift-details/{month_id}/{year_id}/{user_id}', 'HumanResourceController@getUserShiftDetails');
        Route::post('/add-shift', 'HumanResourceController@addShift');
        Route::get('/unassign-shift', 'HumanResourceController@unassignShiftByShiftId');

        Route::group(['prefix' => 'driver'], function () {
            Route::get('/', 'HumanResourceController@getDriverListView');
            Route::get('/get-drivers', 'HumanResourceController@getDriver');
            Route::get('/get-drivers-pending-request', 'HumanResourceController@getDriverPendingRequest');
        });

        Route::group(['prefix' => 'attendant'], function () {
            Route::get('/', 'HumanResourceController@getAttendantListView');
            Route::get('/get-attendants', 'HumanResourceController@getAttendant');
            Route::get('/get-attendants-pending-request', 'HumanResourceController@getAttendantPendingRequest');
        });
        Route::group(['prefix' => 'district-anchor'], function () {
            Route::get('/', 'HumanResourceController@getDistrictAnchorListView');
            Route::get('/get-district-anchors', 'HumanResourceController@getDistrictAnchor');
            Route::get('/delete', 'HumanResourceController@destroyDistrictAnchor');
        });

        Route::group(['prefix' => 'sub-admin'], function () {
            Route::get('/', 'HumanResourceController@getSubAdminListView');
            Route::get('/get-sub-admin', 'HumanResourceController@getSubAdmin');
            Route::get('/delete', 'HumanResourceController@destroySubAdmin');
        });
    });
});
