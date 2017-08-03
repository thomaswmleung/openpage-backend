<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Token_helper;
use App\UsersModel;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller {

    /**
     * @SWG\Post(path="/login",
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
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response($validator->messages(), 400);
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
                $result['user_id'] = $user_data->_id;
            } else {
                $is_valid_user = FALSE;
                $result['error'] = array("Invalid username/password supplied");
                return response(json_encode($result), 400);
            }

            if ($is_valid_user) {
                $token_helper = new Token_helper();
              //  $token = $token_helper->generate_token();
                $token = $token_helper->generate_user_token($user_data->_id);
                $result['token'] = $token;

                return response()->json($result,200);
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
