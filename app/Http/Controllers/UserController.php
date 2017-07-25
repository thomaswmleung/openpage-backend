<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Token_helper;
use App\UsersModel;

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
        } else {
            $user_details = $userModel->user_details();
        }

        return response()->json($user_details);
    }

    public function register(Request $request) {

        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $password = $request->password;
        $username = $request->email;

        // TODO Validation
        
        $user_data = array(
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'username' => $request->email
        );
        
        
        $data = UsersModel::create($user_data);
        // generate activation code
        $activation_key = rand(1111111111,9999999999);
        
        // send mail TODO
        
        return 1;
    }

}
