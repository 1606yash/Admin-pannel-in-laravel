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

Route::middleware(['web', 'auth'])->prefix('ambulance')->group(function () {
    Route::get('/', 'AmbulanceController@index');
    Route::get('/get-all-ambulances', 'AmbulanceController@getAmbulanceList');
    Route::get('/add-ambulance', 'AmbulanceController@addAmbulance');
    Route::post('/store-ambulance', 'AmbulanceController@storeAmbulance');
    Route::post('/update-ambulance-info', 'AmbulanceController@updateAmbulanceInfo');
    Route::post('/store-inventory', 'AmbulanceController@storeInventory');
    Route::get('/view-ambulance-details/{ambulance_id}/', 'AmbulanceController@viewAmbulanceDetails');
    Route::get('/view-expense-details/{ambulance_id}', 'AmbulanceController@viewExpensesByAmbulanceId');
    Route::get('/view-inventory-details/{ambulance_id}/', 'AmbulanceController@viewInventoryByAmbulanceId');
    Route::get('/view-inventory-history/{inventory_id}/', 'AmbulanceController@inventoryHistoryList');
    Route::get('/get-inventory-item-info', 'AmbulanceController@getInventoryItemInfo');
    Route::post('/update-inventory', 'AmbulanceController@updateInventory');
    Route::get('/update-ambulance-status', 'AmbulanceController@updateAmbulanceStatus');
    Route::get('/delete-ambulance', 'AmbulanceController@deleteAmbulance');
    Route::get('/get-users-list', 'AmbulanceController@getUserList');
    Route::get('/get-assign-staff-list', 'AmbulanceController@getAssignStaff');
    Route::get('/get-assign-staff-for-events', 'AmbulanceController@getAssignStaffForEvents');
    Route::post('/assign-shift', 'AmbulanceController@assignShift');

    Route::get('/view-calendar', 'AmbulanceController@calendarView');
});

Route::middleware(['web', 'auth'])->prefix('call-center')->group(function () {
    Route::get('/', 'CaseController@index');
    Route::get('/get-all-cases', 'CaseController@getCases');
    Route::get('/create-case', 'CaseController@createCase');
    Route::post('/store-case', 'CaseController@storeCase');
    Route::post('/assign-driver', 'CaseController@assignDriver');
    Route::get('/get-case-details/{id}/', 'CaseController@getCaseInfo');
    Route::get('/get-case-info', 'CaseController@viewCaseInfo');
    Route::post('/update-case-info', 'CaseController@updateCaseDetails');
    Route::get('/get-drivers', 'CaseController@getAvailableDrivers');
});
