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

Route::get('/', function () {
    return redirect()->route('reportIndex');
});

Auth::routes();

Route::get('/home', 'Web\HomeController@index');

Route::get('/reports', 'Web\ReportsController@index')->name('reportIndex');
Route::get('/reports/{report}', 'Web\ReportsController@view')->name('reportView');
Route::get('/reports/{report}/{action}', 'Web\ReportsController@action')->name('reportAction');
