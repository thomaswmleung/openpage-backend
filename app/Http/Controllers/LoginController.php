<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Token_helper;
use App\UsersModel;


class LoginController extends Controller {

    /**
     * @SWG\Post(path="/login",
     *   tags={"login"},
     *   summary="Logs user into the system",
     *   description="",
     *   operationId="loginUser",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Created user object",
     *     required=true,
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(type="string"),
     *     @SWG\Header(
     *       header="X-Rate-Limit",
     *       type="integer",
     *       format="int32",
     *       description="calls per hour allowed by the user"
     *     ),
     *     @SWG\Header(
     *       header="X-Expires-After",
     *       type="string",
     *       format="date-time",
     *       description="date in UTC when token expires"
     *     )
     *   ),
     *   @SWG\Response(response=400, description="Invalid username/password supplied")
     * )
     */
    public function login(Request $request) {

        $username = $request->username;
        $password = $request->password;
        $data_array = array(
            'username' => $username,
            'password' => $password,
        );

        $user_model = new UsersModel();
        $user_data = $user_model->aunthenticate($data_array);
        $is_valid_user = TRUE;
        if ($user_data != "") {
            $result['user_id'] = $user_data->_id;
        } else {
            $is_valid_user = FALSE;
            $result['error']['ERROR_CODE'] = 'USER_NOT_EXIST';
            $result['error']['ERROR_DESCRIPTION'] = "Incorrect email or password entered";
            return response(400);
        }


        if ($is_valid_user) {
            $token_helper = new Token_helper();
            $token = $token_helper->generate_token();
            $result['token'] = $token;

            return response()->json($result);
        }
    }

    public function log_out(Request $request) {
        $user_id = $request->user_id;
        $token = $request->token;
        // TODO : yet to decide what to do

        return TRUE;
    }

}
