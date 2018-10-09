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
    return view('welcome');
});

Route::get('/oauth', 'Auth\LoginController@oauth');
Route::post('/wx/autologin', 'Auth\LoginController@wxLogin');

Route::group(['middleware' => ['auth.token']], function() {

});

Route::post('/test', 'Auth\LoginController@wxLogin');

