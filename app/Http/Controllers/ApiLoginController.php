<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RestApi;
//use Illuminate\Support\Facades\Session;
//use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Emarref\Jwt\Claim\JwtId;
use Emarref\Jwt\Claim\JwtIdTest;
use Emarref\Jwt\Jwt;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Token;
use Emarref\Jwt\Encryption;
//use Illuminate\Support\Facades\Cookie;
use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Verification\Context;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
//use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\UsersModel;

class ApiLoginController extends Controller {

    // This function handles login functionality
    /* params : Accepts Request Object with json for key "data", 
     * The json will have the below data with the exact key:
     *          "parameters" : {"email": "example@example.com",
     *                          "password" : "password"
     *                          }
      
     * return :  user data along with jwt token if user email and password matches 
                else
                return error code USER_NOT_EXIST with description Incorrect email or password entered.



     */

    public function user_authentication(Request $request) {
		
	
        $request_data = json_decode($request->data, true);
        $required_data = array('email', 'password');

        
        $data_missing_array = array();
        $result = array(
            'error' => array()
        );

        $is_data_insufficient = false;
//      check for required json parameters
        foreach ($required_data as $key) {
            if (!array_key_exists($key, $request_data['parameters'])) {
                $is_data_insufficient = true;
                array_push($data_missing_array, $key);
            }
        }
        $result['user_data'] = "";
        if ($is_data_insufficient == false) {
            $user_model = new UsersModel();

            $email = $request_data['parameters']['email'];
            $password = $request_data['parameters']['password'];
            $login_result = $user_model->aunthenticate($email, $password);

            if ($login_result['count'] == 1) {
                $token = $this->generate_token();
                $login_result['jwt_token'] = $token;
                $result['user_data'] = $login_result;
            } else {
                $result['error']['ERROR_CODE'] = 'USER_NOT_EXIST';
                $result['error']['ERROR_DESCRIPTION'] = "Incorrect email or password entered";
            }
        } else {
            $result['error']['ERROR_CODE'] = 'DATA_INSUFFICIENT';
            $result['error']['ERROR_DESCRIPTION'] = "Insufficient data supplied";
            $result['error']['ERROR_DATA'] = $data_missing_array;
        }
        return $result;
    }

    // This functio used to generate token
    // parms : none
    // returns: a token with current time stamp enrycpted.
    function generate_token() {
        $token = new Token();
        $token->addClaim(new Claim\IssuedAt());

        $current_time = date('Y-m-d H:i');
        $token->addClaim(new Claim\PrivateClaim('timestamp', $current_time));

        $jwt = new Jwt();
        $secret = Config::get('constants.secret');
        $algorithm = new Hs256($secret);
        $encryption = Encryption\Factory::create($algorithm);
        $serializedToken = $jwt->serialize($token, $encryption);
        return $serializedToken;
    }

  

}
