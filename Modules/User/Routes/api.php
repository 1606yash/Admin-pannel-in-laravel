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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});*/

Route::group(['prefix' => 'v1'], function () {
	// retrun version route get 
	Route::get('/', function () {
		return 'v3';
	});
	Route::get('clear', function () {
		Artisan::call('cache:clear');
		Artisan::call('config:clear');
		Artisan::call('config:cache');
		Artisan::call('view:clear');
		Artisan::call('route:clear');
		return "Cleared!";
	});
	Route::post('login', 'Api\V1\AuthController@login');
	
	Route::post('upload-file', 'Api\V1\AuthController@uploadFile');
	Route::post('send-otp', 'Api\V1\AuthController@sendOtp');
	Route::post('verify-otp', 'Api\V1\AuthController@verifyLoginOtp');
	Route::post('create-pin', 'Api\V1\AuthController@createPin');
	Route::group(['middleware' => 'auth:api'], function () {
		Route::post('check-email', 'Api\V1\AuthController@checkEmail');
		Route::post('register', 'Api\V1\UserController@register');
		Route::get('user-details/{user_id?}', 'Api\V1\UserController@getUserDetails');
		Route::post('update-user-details/{user_id?}', 'Api\V1\UserController@updateUserDetails');
		Route::get('logout', 'Api\V1\AuthController@logout');
		Route::get('get-reporting-manager', 'Api\V1\UserController@getReportingManager');
		Route::get('get-ambulances', 'Api\V1\UserController@getAmbulanceMaster');
		Route::get('user-listing', 'Api\V1\UserController@userListing');
		Route::post('approve-reject-user', 'Api\V1\UserController@approveRejectUser');
		Route::post('change-password', 'Api\V1\PasswordResetController@changePassword');
		Route::get('delete-user/{user_id?}', 'Api\V1\UserController@deleteUser');

		// leave routes
		Route::post('get-user-leaves', 'Api\V1\LeaveController@getUserLeaves');
		Route::post('add-leave', 'Api\V1\LeaveController@addLeave');
		Route::get('get-leave-balance', 'Api\V1\LeaveController@getLeaveBalance');

		// resignation routes
		Route::post('add-resignation', 'Api\V1\ResignationController@addResignation');
		Route::get('get-user-resignation', 'Api\V1\ResignationController@getUserResignation');
		Route::get('withdraw-resignation/{id}', 'Api\V1\ResignationController@withdrawResignation');

		Route::post('download-pdf', 'Api\V1\UserController@downloadPdf');


		// employees routes
        Route::post('get-employees', 'Api\V1\EmployeeController@getEmployees');
        Route::post('add-employee', 'Api\V1\EmployeeController@AddEmployee');
        Route::post('get-employees-leave-requests', 'Api\V1\EmployeeController@getEmployeesLeaveRequests');
        Route::post('update-employee-expense-status', 'Api\V1\EmployeeController@updateEmployeeExpenseStatus');
		Route::post('get-employee-attendance', 'Api\V1\EmployeeController@getEmployeeAttendance');

		// documents routes
        Route::get('folders-with-documents', 'Api\V1\DocumentController@getFoldersWithDocuments');
        Route::get('download-document/{id}', 'Api\V1\DocumentController@downloadDocument');
	});

	Route::group(['prefix' => 'password'], function () {
		Route::post('forget-password', 'Api\V1\PasswordResetController@create');
		Route::get('find/{token}', 'Api\V1\PasswordResetController@find');
		Route::post('reset', 'Api\V1\PasswordResetController@reset');
	});

	Route::group(['middleware' => 'auth:api'], function () {

		Route::post('assign-buyer-organiztion', 'Api\V1\UserController@assignBuyerOrganization');

		Route::get('users-by-type/{user_type}', 'Api\V1\UserController@getUsersByType');
		Route::get('retailer-categories', 'Api\V1\UserController@retailerCategories');

		Route::post('map-retailers', 'Api\V1\UserController@storeRetailers');
		Route::get('dsp-retailers/{dsp}', 'Api\V1\UserController@getDspRetailers');
		Route::get('user-details/{user_id}', 'Api\V1\UserController@userDetails');
		Route::post('user-status', 'Api\V1\UserController@updateUserStatus');
		Route::get('distributor-details', 'Api\V1\UserController@distributorDetails');
		Route::get('/users/{id}/address', 'Api\V1\AddressController@userAddresses');
		Route::post('contact', 'Api\V1\UserController@contact');

		Route::get('all-buyers', 'API\V1\WmController@getAllBuyers');
	});
});
