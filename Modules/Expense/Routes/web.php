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
    Route::group(['prefix' => 'expenses'], function () {
        Route::get('/', 'ExpenseController@index');
        Route::get('/get-expense-list', 'ExpenseController@getExpenseList');
        Route::get('/export-expense-grid', 'ExpenseController@exportGrid');
        Route::get('/expense-details/{id}/', 'ExpenseController@viewExpenseDetails');
        Route::get('/approve-reimbursement', 'ExpenseController@approveReimbursement');
        Route::post('/reject-claim-entry', 'ExpenseController@rejectReimbursement');
    });
});
