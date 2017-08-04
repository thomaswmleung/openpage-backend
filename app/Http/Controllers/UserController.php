<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Token_helper;
use App\UsersModel;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EmailController;

class UserController extends Controller {
    /**
     * @SWG\Get(path="/user",
     * tags={"User"},
     *   summary="Returns list of users",
     *   description="Returns users data",
     *   operationId="user",
     *   produces={"application/json"},
     *   parameters={},
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */

    /**
     * @SWG\Get(path="/user/{uid}",
     * tags={"User"},
     *   summary="Returns users data",
     *   description="Returns users data",
     *   operationId="user",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ID of the user that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid user id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function user(Request $request) {
        $userModel = new UsersModel();
        if (isset($request->uid)) {
            $user_id = $request->uid;
            // get user details
            $data_array = array(
                '_id' => $user_id
            );
            $user_details = $userModel->user_details($data_array);
            if ($user_details == NULL) {
                $error['error'] = array("Invalid user id");
                return response(json_encode($error), 400);
            }
        } else {
            $user_details = $userModel->user_details();
        }

        return response(json_encode($user_details), 200);
    }

     /**
     * @SWG\Post(path="/register",
      * tags={"User"},
     *   summary="User registration into the system",
     *   description="User registration into the system",
     *   operationId="register",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="first_name",
     *     in="query",
     *     description="First name of user",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="last_name",
     *     in="query",
     *     description="Last name of user",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     description="Email address of user",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     description="User secured password",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=400, description="Invalid data supplied")
     * )
     */
    public function register(Request $request) {
        $user_data = array(
            'first_name' => trim($request->first_name),
            'last_name' => trim($request->last_name),
            'email' => strtolower(trim($request->email)),
            'password' => trim($request->password),
        );
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required',
            'email' => 'required|email|unique:users,email'
        );
        $validator = Validator::make($user_data, $rules);
        if ($validator->fails()) {
            return response($validator->messages(), 400);
        } else {

            $user_data['username'] = strtolower(trim($request->email));
            // generate activation code
            $user_data['activation_key'] = rand(123456789, 987456321);
            $data = UsersModel::create($user_data);
            // Send an email
            $activation_url = url('activate') . "?username=" . $user_data['username'] . "&key=" . $user_data['activation_key'];
            $content = "Please click the link to activate your <a target='new' href='" . $activation_url . "'> account</a><br>$activation_url";
            $email_data = array(
                'to_email' => 'suraj@aalpha.net',
                'from_email' => 'info@aalpha.net',
                'username' => $user_data['first_name'] . ' ' . $user_data['last_name'],
                'content' => $content,
            );
            //Need SMTP credentials to send Email from instance
//            EmailController::send_activation_mail($email_data);

            return response("User created successfully.", 200);
        }
    }

     /**
     * @SWG\Get(path="/activate",
      * tags={"User"},
     *   summary="Activates user into the system",
     *   description="Activates user into the system",
     *   operationId="activate",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     description="User name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="key",
     *     in="query",
     *     description="Activation key",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=400, description="Invalid data supplied")
     * )
     */
    function activate(Request $request) {
        $username = trim($request->username);
        $activation_key = trim($request->key);
        if ($activation_key != "") {
            $activation_key = (int) $activation_key;
        }
        $user_data = array(
            'username' => $username,
            'activation_key' => $activation_key
        );
        $rules = array(
            'username' => 'required|exists:users,username',
            'activation_key' => 'required|exists:users,activation_key'
        );
        $validator = Validator::make($user_data, $rules);
        if ($validator->fails()) {
            return response($validator->messages(), 400);
        } else {
            $this->activate_user($user_data);
            return response("Activated", 200);
        }
    }

    function activate_user($data) {
        $usersModel = new UsersModel();
        return $usersModel->activate_user($data);
    }

}
