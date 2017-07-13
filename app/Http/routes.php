<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', 'loginController@getIndex');
Route::controller('login', 'loginController');


Route::group(['middleware' => 'login_auth'], function () {
   Route::controller('home', 'homeController'); 
});


Route::post('api_check_user_login', 'RestApi\ApiLoginController@check_user_login');
Route::post('register_user', 'RestApi\ApiLoginController@register_user');
Route::post('api_check_unique_user_email', 'RestApi\ApiLoginController@check_unique_user_email');

Route::get('fpdf', 'PdfController@generate_pdf');
