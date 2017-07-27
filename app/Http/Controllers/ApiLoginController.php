<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RestApi;
use Illuminate\Support\Facades\Input;
use Emarref\Jwt\Claim\JwtId;
use Emarref\Jwt\Claim\JwtIdTest;
use Emarref\Jwt\Jwt;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Token;
use Emarref\Jwt\Encryption;
use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Verification\Context;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\UsersModel;

class ApiLoginController extends Controller {

    // This function handles login functionality
    /* params : Accepts Request Object with json for key "data", 
     * The json will have the below data with the exact key:
     *          "parameters" : {
     *                          "email": "example@example.com",
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
                $token_helper = new Token_helper();
                $token = $token_helper->generate_token();
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

    // This function used to generate token
    // parms : none
    // returns: a token with current time stamp enrycpted.


    /**
 * @SWG\Get(
 *   path="/api_user_info/{customerId}",
 *   summary="List customer details",
 *   operationId="user_information",
 *   @SWG\Parameter(
 *     name="customerId",
 *     in="path",
 *     description="Target customer.",
 *     required=true,
 *     type="integer"
 *   ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *   @SWG\Response(response=500, description="internal server error")
 * )
 *
 */
    public function user_information(Request $request) {
        $customerId = $request->customerId;
        return $customerId;
    }

}
