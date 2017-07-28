<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Token_helper;
use App\UsersModel;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EmailController;

class UserController extends Controller {

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
                return response(json_encode($error), "400");
            }
        } else {
            $user_details = $userModel->user_details();
        }

        return response(json_encode($user_details), "200");
    }

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
            $content = "Please click the link to activate your <a target='new' href='" . $activation_url . "'> account</a> ";
            $email_data = array(
                'to_email' => 'suraj@aalpha.net',
                'from_email' => 'info@aalpha.net',
                'username' => $user_data['first_name'] . ' ' . $user_data['last_name'],
                'content' => $content,
            );
            EmailController::send_activation_mail($email_data);

            return response("User created successfully. Email is sent", 200);
        }
    }

    function activate(Request $request) {
        $user_data = array(
            'username' => trim($request->username),
            'activation_key' => trim($request->key)
        );
        $rules = array(
            'username' => 'required|exists:users,username',
            'activation_key' => 'required'
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
