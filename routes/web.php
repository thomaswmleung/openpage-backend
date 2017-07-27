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



Route::post('login', 'LoginController@login');
Route::get('log_out', 'LoginController@log_out');

Route::get('test', 'UserController@test');
Route::get('user/{uid}', 'UserController@user');
Route::get('user', 'UserController@user');
Route::post('register', 'UserController@register');

Route::group(['middleware' => 'rest_api_auth'], function () {

    // This Middleware will be used for the pages where login of user is not mandatory
    Route::group(['middleware' => 'non_login_auth'], function () {
        //
        Route::post('api_check_user_login', 'ApiLoginController@user_authentication');
    });

    // This Middleware will be used to check the token if it was generated within given time ago
    Route::group(['middleware' => 'login_auth'], function () {
    // Route::post('api_user_info', 'ApiLoginController@user_information');
    });
});

Route::get('fpdf', 'PdfController@generate_pdf');


Route::get('page_group', 'PageGroupController@create_page_group');

Route::get('api_user_info/{id}', 'ApiLoginController@user_information');


