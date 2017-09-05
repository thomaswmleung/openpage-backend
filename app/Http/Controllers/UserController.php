<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Token_helper;
use App\UsersModel;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EmailController;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Log;

class UserController extends Controller {
    /**
     * @SWG\Get(path="/user",
     * tags={"User"},
     *   summary="Returns list of users",
     *   description="Returns users data",
     *   operationId="user",
     *   produces={"application/json"},
     *   parameters={},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search parameter or key word to search",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="skip",
     *     in="query",
     *     description="this is offset or skip the records",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of records to be retrieved ",
     *     type="integer"
     *   ),
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
            $user_details = $userModel->find_user_details($user_id);
            if ($user_details == NULL) {
                $error['error'] = array("Invalid user id");

                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_user_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_user_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $user_details, "errors" => array());
                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
            }
        } else {
            $search_key = "";
            if (isset($request->search_key)) {
                $search_key = $request->search_key;
            }
            $skip = 0;
            if (isset($request->skip)) {
                $skip = (int)$request->skip;
            }
            $limit = config('constants.default_query_limit');
            if (isset($request->limit)) {
                $limit = (int)$request->limit;
            }
            $query_details = array(
                'search_key' => $search_key,
                'limit' => $limit,
                'skip' => $skip
            );

            $user_details = $userModel->user_details($query_details);
            $total_count = $userModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $user_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
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
            'email' => 'min:3|required|email|unique:users,email'
        );

        $messages = [
            'first_name.required' => config('error_constants.first_name_required'),
            'last_name.required' => config('error_constants.last_name_required'),
            'email.email' => config('error_constants.email_vaild'),
            'email.required' => config('error_constants.email_required'),
            'email.unique' => config('error_constants.email_already_taken'),
            'password.required' => config('error_constants.password_required')
        ];


        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($user_data, $rules, $formulated_messages);

        if ($validator->fails()) {
            dd($validator->messages());
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
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
            $responseArray = array("success" => TRUE, "data" => array("_id" => $data->_id), "message" => "User created successfully.");
            return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
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
        $messages = [
            'username.required' => config('error_constants.username_required'),
            'activation_key.required' => config('error_constants.activation_key_required'),
            'activation_key.exists' => config('error_constants.activation_key_exists'),
        ];


        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);
        $validator = Validator::make($user_data, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            $this->activate_user($user_data);
            $responseArray = array("success" => TRUE);
            return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
        }
    }

    function activate_user($data) {
        $usersModel = new UsersModel();
        return $usersModel->activate_user($data);
    }

}
