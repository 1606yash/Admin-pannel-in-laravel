<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

/*Route::get('/', function () {
    return view('welcome');
});*/


// View's
Auth::routes();

// Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/', '\Modules\Dashboard\Http\Controllers\DashboardController@index');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);


Route::get('/terms', [App\Http\Controllers\HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\HomeController::class, 'privacy'])->name('privacy');
Route::get('/help', [App\Http\Controllers\HomeController::class, 'help'])->name('help');

Auth::routes();
Route::post('post-login', 'App\Http\Controllers\Auth\LoginController@postLogin');
Route::post('post-logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('post-logout');
Route::group(['prefix' => 'password'], function () {
    Route::post('send-link', 'App\Http\Controllers\Auth\ResetPasswordController@sendPasswordLink');
    Route::post('reset-password', 'App\Http\Controllers\Auth\ResetPasswordController@reset_password');
});
Route::get('forgot-password/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@find');

//get state 
Route::get('/get-state', 'App\Http\Controllers\CommonController@getState');
Route::get('/get-reporting-manager', 'App\Http\Controllers\CommonController@getReportingManagerForDriver');
Route::get('/get-users', 'App\Http\Controllers\CommonController@getUsersByRoleId');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/error', function () {
    return view('error/403');
});



Route::get('cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
$router->get('/view', function () use ($router) {
    return "V.1.01" . " View";
});
