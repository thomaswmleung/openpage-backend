<?php

namespace App\Http\Middleware;

use Redirect;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Helpers\Token_helper;
use DateTime;
use DateInterval;

class LoginAuth {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * params : Accepts Request Object with json for key "data",
     * The json will have the below data with the exact key: "token" ,
     *  the value to the token will be the token fetched in the earlier successfull api calls

     * @returns: 
     * if the timestamp fetched from the token doesn't fall within said time interval 
     * then it return the error code 'TOKEN_EXPIRATION_ERROR' 
     * else it processes the request to controller.
     */
    public function handle($request, Closure $next) {

        
     
//            $data = json_decode($request->data, true);
        //if (isset($data['token']) && $data['token'] != "") {
        if ($request->header('token') && $request->header('token') != "") {

            // Verify if the token is valid
            $token = $request->header('token');
            $token_helper = new Token_helper;
            $is_token_valid = $token_helper->verify_token($token);
            if ($is_token_valid) {
                $token = $token_helper->token_decode($token);
                $token_object = json_decode($token);
                $token_string = $token_object->data;

//                $timestamp = $token_object->timestamp;
//                $uid = $token_object->uid;
//                dd($uid);
                
                /***/
                $token_user = explode("||", $token_string);
                 $timestamp = $token_user[0];
                 $uid = $token_user[1];
                
                
                /***/
                
                
                $time_interval = Config::get('constants.time_interval');
                $time = new DateTime($timestamp);
                $time->add(new DateInterval('PT' . $time_interval . 'M'));
                $expiration_time = $time->format('Y-m-d H:i');
                $current_time = date('Y-m-d H:i');


                if ($current_time > $expiration_time) {
                    $result = array();
                    $result['error']['ERROR_CODE'] = 'TOKEN_EXPIRATION_ERROR';
                    $result['error']['ERROR_DESCRIPTION'] = "Token expired";
                    return response(json_encode($result),400);
                } else {
                    $response = $next($request);
                    //$token = $token_helper->generate_token();
                   
                    $token = $token_helper->generate_user_token($uid);
                   
                    $response->token = $token;
                    
                    return $response;
                }
            } else {
                
                $result = array();
                $result['error']['ERROR_CODE'] = 'INVALID_TOKEN_ERROR';
                $result['error']['ERROR_DESCRIPTION'] = "Token invalid";
                return response(json_encode($result),400);
            }
        } else {
            $result = array();
            $result['error']['ERROR_CODE'] = 'INVALID_TOKEN_ERROR';
            $result['error']['ERROR_DESCRIPTION'] = "Token not found";
            return response(json_encode($result),400);
        }
    }

}
