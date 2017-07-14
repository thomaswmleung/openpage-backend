<?php

namespace App\Http\Controllers\RestApi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RestApi;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Emarref\Jwt\Claim\JwtId;
use Emarref\Jwt\Claim\JwtIdTest;
use Emarref\Jwt\Jwt;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Token;
use Emarref\Jwt\Encryption;
use Illuminate\Support\Facades\Cookie;
use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Verification\Context;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\Helpers\Sms_helper;
use App\UsersModel;

class ApiLoginController extends Controller {
    
    
    // This function handles login functionality

    public function check_user_login(Request $request) {
        $request_data = json_decode($request->data, true);
        $token = $this->handle_token();

        $required_data = array('email', 'password');
        
        if ($token) {
            $login_data = $request_data['parameters'];
            $mandatoryKeys = array();
            $result = array(
                'error' => array()
            );

            $data_insufficient = false;
//            check for required json parameters
            foreach ($required_data as $key) {
                if (!array_key_exists($key, $login_data)) {
                    $data_insufficient = true;
                    array_push($mandatoryKeys, $key);
                }
            }

            if ($data_insufficient == false) {
                $userModel = new UsersModel();

                $email = $request_data['parameters']['email'];
                $password = $request_data['parameters']['password'];
                $login_result = $userModel->login_check($email, $password);
                
                if ($login_result['count'] == 1) {
                    $login_result['jwt_token'] = $token['serializedToken'];
                }
                $result['user_data'] = $login_result;

            } else {
                $result['error']['ERROR_CODE'] = 'DATA_INSUFFICIENT';
                $result['error']['ERROR_DESCRIPTION'] = "Insufficient data supplied";
                $result['error']['ERROR_DATA'] = $mandatoryKeys;
            }
        } else {
            $result = array(
                'error' => array(
                    "ERROR_CODE" => "TOKEN_MISMATCH",
                    "ERROR_DESCRIPTION" => "Token does not found, Please try after some time."
                )
            );
        }
       
        return $result;
    }

    // this function handles and validates tokens
    function handle_token() {
        $response = new Response();
        $secret_key_error = false;
        $serializedToken = Cookie::get('token');

        if ($serializedToken == NULL) {
            //generate token
            $serializedToken = $this->generate_token();
//            $cookie = Cookie::queue('token', $serializedToken, 1);
            $secret_key_error = false;
        } else {
            //verify token from cookie
            $verify_result = $this->verify_token($serializedToken);
            if (!$verify_result) {

                //generate token
                $serializedToken = $this->generate_token();
//                $cookie = Cookie::queue('token', $serializedToken, 1);
                $secret_key_error = true;
            }
        }

        $return_data = array(
            'serializedToken' => $serializedToken,
            'secret_key_error' => $secret_key_error
        );

        return $return_data;
    }

    // This functio used to generate token
    function generate_token() {
        $token = new Token();
        $token->addClaim(new Claim\IssuedAt());
        if (session('user_info')) {
            $token->addClaim(new Claim\PrivateClaim('user', session('user_info')['first_name']));
        } else {
            $token->addClaim(new Claim\PrivateClaim('user', 'guest'));
        }

        $jwt = new Jwt();
        $secret = Config::get('constants.secret');
        $algorithm = new Hs256($secret);
        $encryption = Encryption\Factory::create($algorithm);
        $serializedToken = $jwt->serialize($token, $encryption);
        return $serializedToken;
    }

    // this function used to verify tokens
    public function verify_token($token) {

        $jwt = new Jwt();
        $de_token = $jwt->deserialize($token);

        $secret = Config::get('constants.secret');
        $algorithm = new Hs256($secret);
        $encryption = Encryption\Factory::create($algorithm);

        $context = new Context($encryption);
        try {
            $verify_result = $jwt->verify($de_token, $context);
        } catch (VerificationException $e) {
            $verify_result = FALSE;
        }
        return $verify_result;
    }

    // this function handles registeration of users 
    public function register_user(Request $request) {
        $request_data = json_decode($request->data, true);
        $required_data = array('first_name', 'last_name', 'email', 'password', 'contact_number');
        $token_helper = new Token_helper();
        $verification_result = $token_helper->verify_token($request_data['token']);


        if ($verification_result) {
            $userModel = new UsersModel();
            $userModelData = $userModel->getFillable();

            $user_data = $request_data['user_data'];

            $mandatoryKeys = array();
            $inAppropriateKeys = array();

            $result = array(
                'error' => array()
            );
            $is_key_exist = true;
            foreach ($user_data as $key => $value) {
                if (!in_array($key, $userModelData)) {
                    $is_key_exist = false;
                    array_push($inAppropriateKeys, $key);
                }
            }

            if ($is_key_exist == false) {
                $result['error']['ERROR_CODE'] = 'IN_APPROPRIATE_JSON_KEY';
                $result['error']['ERROR_DESCRIPTION'] = "Inappropriate json data supplied";
                $result['error']['ERROR_DATA'] = $inAppropriateKeys;
                return $result;
            } $data_insufficient = false;
//            check for required json parameters
            foreach ($required_data as $key) {
                if (!array_key_exists($key, $user_data)) {
                    $data_insufficient = true;
                    array_push($mandatoryKeys, $key);
                }
            }
            if ($data_insufficient == false) {

                if ($request->image != NULL) {
                    $profile_img = Input::file('image');
                    $destinationPath = public_path() . '/candidates_cv/';
                    $fileName = date('Y.m.d') . time() . str_random(5) . $profile_img->getClientOriginalName();
                    $file_name = str_replace(" ", "_", $fileName);
                    $upload_image = $profile_img->move($destinationPath, $file_name);

                    $user_data['resume_file'] = $file_name;
                } else {
                    $user_data['resume_file'] = "";
                }
                $user_info = $userModel->register_user($user_data);
                $result['user_data'] = $user_info;
            } else {
                $result['error']['ERROR_CODE'] = 'DATA_INSUFFICIENT';
                $result['error']['ERROR_DESCRIPTION'] = "Insufficient data supplied";
                $result['error']['ERROR_DATA'] = $mandatoryKeys;
            }
        } else {
            $result = array(
                'error' => array(
                    "ERROR_CODE" => "TOKEN_MISMATCH",
                    "ERROR_DESCRIPTION" =>
                    "Token does not matched, Please try after some time."
                )
            );
        }
   
        return $result;
    }

    // this function check duplication of email
    public function check_unique_user_email(Request $request) {
        $request_data = json_decode($request->data, true);
        $required_data = array('email');

        if (isset($request_data['token']) && $request_data['token'] != null) {
            $token_helper = new Token_helper();
            $verification_result = $token_helper->verify_token($request_data['token']);

            if ($verification_result) {
                $userModel = new UsersModel();
                $parameter = $request_data['parameters'];

                $email = $parameter['email'];
                $mandatoryKeys = array();

                $result = array(
                    'error' => array()
                );

                $data_insufficient = false;
//            check for required json parameters
                foreach ($required_data as $key) {
                    if (!array_key_exists($key, $parameter)) {
                        $data_insufficient = true;
                        array_push($mandatoryKeys, $key);
                    }
                }
                if ($data_insufficient == false) {
                    $count = $userModel->check_unique_email($parameter);
                    $result['count'] = $count;
                } else {
                    $result['error']['ERROR_CODE'] = 'DATA_INSUFFICIENT';
                    $result['error']['ERROR_DESCRIPTION'] = "Insufficient data supplied";
                    $result['error']['ERROR_DATA'] = $mandatoryKeys;
                }
            } else {
                $result = array(
                    'error' => array(
                        "ERROR_CODE" => "TOKEN_MISMATCH",
                        "ERROR_DESCRIPTION" => "Token does not matched, Please try after some time."
                    )
                );
            }
        } else {
            $result = array(
                'error' => array(
                    "ERROR_CODE" => "TOKEN_MISMATCH",
                    "ERROR_DESCRIPTION" => "Token  not found, Please try after some time."
                )
            );
        }
        return $result;
    }

}
