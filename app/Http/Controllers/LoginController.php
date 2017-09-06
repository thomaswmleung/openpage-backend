<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Token_helper;
use App\UsersModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ErrorMessageHelper;
class LoginController extends Controller {

    /**
     * @SWG\Post(path="/login",
     *  tags={"Login"},
     *   summary="Logs user into the system",
     *   description="",
     *   operationId="loginUser",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     description="The user name for login",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     description="The password for login in clear text",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=400, description="Invalid username/password supplied")
     * )
     */
    public function login(Request $request) {
       
        $rules = array(
            'username' => 'required',
            'password' => 'required'
        );
        
        $messages=[
            'username.required' => config('error_constants.login_user_name_required'),
            'password.required' => config('error_constants.login_password_required')
            
        ];
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);
//        var_dump($formulated_messages);
//        exit();
        $validator = Validator::make($request->all(), $rules,$formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            $username = trim($request->username);
            $password = trim($request->password);
            $data_array = array(
                'username' => $username,
                'password' => $password,
            );
            
            $user_model = new UsersModel();
            $user_data = $user_model->aunthenticate($data_array);
            $is_valid_user = TRUE;
            if (count($user_data) != 0) {
                $result['_id'] = $user_data->_id;
            } else {
                $is_valid_user = FALSE;
                $error_messages = array(array("ERR_CODE" => config('error_constants.login_invalid')['error_code'],
                                        "ERR_MSG"=> config('error_constants.login_invalid')['error_message'])) ;       
                
                
                $responseArray = array("success" => FALSE, "errors" => $error_messages);
                
                
                return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
            }

            if ($is_valid_user) {
                $token_helper = new Token_helper();
              //  $token = $token_helper->generate_token();
                $token = $token_helper->generate_user_token($user_data->_id);
                $result['token'] = $token;
                $responseArray = array("success" => TRUE, "data" => $result);
                return response(json_encode($responseArray),200)->header('Content-Type', 'application/json');
            }
        }
    }

    public function log_out(Request $request) {
        $user_id = $request->user_id;
        $token = $request->token;
        // TODO : yet to decide what to do

        return "TRUE";
    }

}
