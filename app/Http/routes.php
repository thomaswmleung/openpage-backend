<?php


    Route::group(['middleware' => 'rest_api_auth'], function () {
        
         // This Middleware will be used for the pages where login of user is not mandatory
        Route::group(['middleware' => 'non_login_auth'], function () {
            //
            Route::post('api_check_user_login', 'ApiLoginController@user_authentication');
          

            
        });
        
        // This Middleware will be used to check the token if it was generated within given time ago
        Route::group(['middleware' => 'login_auth'], function () {
           
        });
    });




