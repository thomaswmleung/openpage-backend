<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\loginController;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Curl_helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Language_helper;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/*
 *  Class Name : loginController
 *  Description : This controller handles login and registration functionality
 * 
 * 
 */

class loginController extends Controller {

    // This function helps to display login page
    public function getIndex() {

        $label_page = "login_page_lables";

        $language_helper = new Language_helper();
        $errorMsgArray = $language_helper->get_js_messages($label_page);
        $info_array = array(
            'js_errors' => $errorMsgArray
        );

        return view('login', $info_array);
    }

    // This function handles login funtionality
    public function postLoginUser(Request $request) {

        $email = strtolower($request->input('inputEmail'));

        $password = $request->input('inputPassword');
        $url = Config::get('constants.rest_api_url') . "/api_check_user_login";

        $data_array = array(
            'email' => $email,
            'password' => $password,
        );
        $data = array(
            'parameters' => $data_array
        );
        $data_e = array(
            'data' => json_encode($data)
        );
        $data['type'] = json_encode('WEB');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_e);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result_status = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($result_status, true);



        if (count($result['error']) == 0) {

            if ($result['user_data']['count'] == 1) {

                if ($result['user_data'] != "" && $result['user_data'] != null) {
                 
                    // set the session and redirect to hompage
                    session(['user_info' => $result['user_data']]);
                    $session_info = Session::get('user_info');
                    return response()->json(['success' => 'true', 'success_msg' => 'Logged in successfully']);
                } else {
                    return response()->json(['success' => 'false', 'error' => 'Your Account is deactivated.']);
                }
            } else {

                // redirect to login page
                $error = array('error_message' => "Incorrect email or password entered", 'email_entered' => $email, 'password_entered' => $password);
                // return view('login')->with($error);
                return response()->json(['success' => 'false', 'error' => $error]);
            }
        } else {
            $error = array('error' => 'Something went wrong. please try again after some time.');
            return response()->json(['success' => 'false', 'error' => $error]);
//            return view('login')->with($error);
        }
    }

    // this functio handles user registration functionality
    public function postUserRegistration(Request $request) {

        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'retype_password' => 'required'
        );


        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json(['success' => 'false', 'required_error' => json_encode($validator->errors())]);
        } else {

            $user_data = array(
                'first_name' => trim($request->first_name),
                'last_name' => trim($request->last_name),
                'email' => trim($request->email),
                'password' => trim($request->password),
                'contact_number' => trim($request->contact_number),
                'resume_text' => trim($request->resume_text),
            );


            $url = Config::get('constants.rest_api_url') . "/register_user";
            $data = array(
                'user_data' => $user_data
            );
            $curl_helper = new Curl_helper();


            if (isset($_FILES['resume_file']['name'])) {
                $resume_file = $_FILES['resume_file']['name'];
                $resume_file_size = $_FILES['resume_file']['size'];

                $rule = array(
                    'resume_file' => 'mimes:pdf,docx',
                    'resume_file' => 'max:500000',
                );
                $validator = Validator::make(Input::all(), $rule);
                if ($validator->fails()) {

                    return response()->json(['success' => 'false', 'file_error' => 'Please choose valid file']);
                } else {
                    $tmpfile = $_FILES['resume_file']['tmp_name'];
                    $filename = basename($_FILES['resume_file']['name']);
                    $args['uploaded_file'] = curl_file_create($_FILES['resume_file']['tmp_name'], 'pdf', $filename);
                }

                $registration_result = $curl_helper->setup_curl($data, $url, $args['uploaded_file']);
            } else {
                $registration_result = $curl_helper->setup_curl($data, $url);
            }



            $register_info = json_decode($registration_result, true);



            if (count($register_info['error']) == 0) {
                $user_id = $register_info['user_data']['_id'];

                if ($user_id) {
                    // set the session and redirect to home page
                    session(['user_info' => $register_info['user_data']]);
                    $session_info = Session::get('user_info');

                    return response()->json(['success' => 'true', 'success_msg' => 'Registration done successfully']);
                } else {
                    return response()->json(['success' => 'false', 'error' => 'Something went wrong, please try after sometime']);
                }
            } else {
                return response()->json(['success' => 'false', 'error' => 'Something went wrong, please try after sometime']);
            }
        }
    }

    // this function clears the sessions data for logout operation
    public function getLogout(Request $request) {
        $request->session()->flash('success', 'You have successfully logged out.');
        $request->session()->forget('user_info');

        return Redirect::to('login');
    }

    
    // this function check duplication of email id
    public function getCheckUniqEmail(Request $request) {

        $email = array(
            'email' => trim($request->email),
        );
        $url = Config::get('constants.rest_api_url') . "/api_check_unique_user_email";

        $data = array(
            'parameters' => $email
        );
        $curl_helper = new Curl_helper();
        $search_result = $curl_helper->setup_curl($data, $url);
        $result = json_decode($search_result, true);
        if ($result['count'] == 0) {
            echo "true";
        } else {
            echo "false";
        }
    }

}
