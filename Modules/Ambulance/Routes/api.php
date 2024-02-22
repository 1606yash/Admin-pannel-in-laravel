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

Route::group(['prefix' => 'v1/ambulance'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('get-ambulances', 'Api\V1\AmbulanceController@getAmbulances');
        Route::get('ambulance-details/{id?}', 'Api\V1\AmbulanceController@getAmbulanceDetails');
        Route::post('get-ambulance-staff', 'Api\V1\AmbulanceStaffController@getAmbulanceStaff');
        Route::post('add-ambulance-staff', 'Api\V1\AmbulanceStaffController@addAmbulanceStaff');
        Route::get('get-ambulance-inventories', 'Api\V1\AmbulanceInventoryController@getAmbulanceInventories');
        Route::post('add-ambulance-inventory', 'Api\V1\AmbulanceInventoryController@addAmbulanceInventory');
        Route::post('get-ambulance-inventories-by-id', 'Api\V1\AmbulanceInventoryController@getAmbulanceInventoriesById');
        Route::post('update-ambulance-inventory', 'Api\V1\AmbulanceInventoryController@updateAmbulanceInventory');
        Route::post('get-ambulance-expenses', 'Api\V1\AmbulanceExpenseController@getAmbulanceExpenses');
        Route::post('update-ambulance-expense-status', 'Api\V1\AmbulanceExpenseController@updateAmbulanceExpenseStatus');

        Route::post('accept-reject-case', 'Api\V1\CaseController@acceptRejectCase');
        Route::post('patient-registration', 'Api\V1\CaseController@patientRegistration');
        Route::post('get-cases', 'Api\V1\CaseController@getCases');
        Route::post('update-case-status', 'Api\V1\CaseController@updateCaseStatus');
        Route::get('case-statistics/{user_id?}', 'Api\V1\CaseController@getCaseStatistics');
    });
});