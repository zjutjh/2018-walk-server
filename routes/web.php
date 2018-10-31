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
Route::get('/wx/getUserInfo', 'Auth\LoginController@getUserInfo');



Route::get('/index/info', 'IndexController@indexInfo');
Route::get('/info/count', 'IndexController@count');
Route::get('/result', 'GroupController@result');
Route::group(['middleware' => ['auth.token', 'check.finish']], function() {
    Route::post('/login/stu', 'UserController@verifyStu');
    Route::post('/login/other', 'UserController@verifyOther');
    Route::post('/user/detail', 'UserController@detailInfo');

    Route::any('/team/list', 'GroupController@groupLists');
    Route::get('/team/apply/list', 'GroupController@getApplyList');
    Route::get('/team/apply/count', 'GroupController@getApplyCount');
    Route::post('/team/create', 'GroupController@createGroup');
    Route::post('/team/update', 'GroupController@updateGroupInfo');
    Route::get('/team/break', 'GroupController@breakGroup');
    Route::post('/team/apply', 'GroupController@doApply');
    Route::get('/team/leave', 'GroupController@leaveGroup');
    Route::get('/team/lock', 'GroupController@lockGroup');
    Route::get('/team/unlock', 'GroupController@unlockGroup');
    Route::post('/team/agree', 'GroupController@agreeMember');
    Route::post('/team/refuse', 'GroupController@refuseMember');
    Route::any('/team/search', 'GroupController@searchTeam');
    Route::post('/team/delete', 'GroupController@deleteMember');
    Route::get('/team/info', 'GroupController@getGroupInfo');
    Route::get('/team/members', 'GroupController@getGroupMembers');
    Route::get('/apply/team', 'GroupController@getApplyTeam');
    Route::get('/apply/delete', 'GroupController@deleteApply');






});
Route::get('/get/end', 'IndexController@verifyApplyEnd');

Route::get('/user/download', 'UserController@download')->name('user');
Route::get('/group/download', 'IndexController@teamDownload')->name('group');
Route::get('/qq', 'IndexController@toQq');
Route::post('/user/send', 'IndexController@sendTmp')->name('send');
Route::get('/admin/send', function() {
   return view('send');
});

Route::post('/test', 'Auth\LoginController@wxLogin');

