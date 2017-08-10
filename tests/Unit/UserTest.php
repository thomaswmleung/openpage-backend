<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use App\UsersModel;

class UserTest extends TestCase {

    /**
     * A basic test example.
     *
     * @return void
     */
    var $current_user_first_name;
    var $current_user_last_name;
    var $current_user_email;
    var $current_user_name;
    var $current_user_password;

    public function __construct() {
        parent::__construct();
        $this->stub_user();
    }

    public function testUserCreationSuccess() {
        $user_details = $this->stub_user();
        $user_details = array(
            'first_name' => $this->current_user_first_name,
            'last_name' => $this->current_user_last_name,
            'email' => $this->current_user_email,
            'password' => $this->current_user_password,
        );

        $response_json = $this->create_user($user_details);
        $response_array = json_decode($response_json, true);

        $is_success = FALSE;
        if (isset($response_array['errors']) AND sizeof($response_array['errors']) > 0) {
            $is_success = FALSE;
        }

        if ($response_array['success'] == TRUE) {
            $is_success = TRUE;
        }
        $this->assertTrue($is_success);
    }

    public function testUserCreationForBlankFirstName() {
        $randomNumber = rand(10, 10000);
        $user_details = array(
            'first_name' => '',
            'last_name' => 'SampleUser',
            'email' => 'sampleuser' . $randomNumber . '@samplemail.com',
            'password' => '123456',
        );
        $response_json = $this->create_user($user_details);
        $response_array = json_decode($response_json, true);

        $is_success = FALSE;
        if (isset($response_array['errors']) AND sizeof($response_array['errors']) > 0) {
            foreach ($response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.first_name_required')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }

        $this->assertTrue($is_success);
    }

    public function testUserCreationForBlankLastName() {
        $randomNumber = rand(10, 10000);
        $user_details = array(
            'first_name' => 'SampleUser',
            'last_name' => '',
            'email' => 'sampleuser' . $randomNumber . '@samplemail.com',
            'password' => '123456',
        );
        $response_json = $this->create_user($user_details);
        $response_array = json_decode($response_json, true);

        $is_success = FALSE;
        if (isset($response_array['errors']) AND sizeof($response_array['errors']) > 0) {
            foreach ($response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.last_name_required')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }

        $this->assertTrue($is_success);
    }

    public function testUserCreationForInvalidEmail() {
        $randomNumber = rand(10, 10000);
        $user_details = array(
            'first_name' => 'SampleUser',
            'last_name' => 'SampleUser',
            'email' => 'invalidemail',
            'password' => '123456',
        );
        $response_json = $this->create_user($user_details);
        $response_array = json_decode($response_json, true);

        $is_success = FALSE;
        if (isset($response_array['errors']) AND sizeof($response_array['errors']) > 0) {
            foreach ($response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.email_vaild')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }

        $this->assertTrue($is_success);
    }

    public function testUserCreationForBlankEmail() {
        $randomNumber = rand(10, 10000);
        $user_details = array(
            'first_name' => 'SampleUser',
            'last_name' => 'SampleUser',
            'email' => '',
            'password' => '123456',
        );
        $response_json = $this->create_user($user_details);
        $response_array = json_decode($response_json, true);

        $is_success = FALSE;
        if (isset($response_array['errors']) AND sizeof($response_array['errors']) > 0) {
            foreach ($response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.email_required')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }

        $this->assertTrue($is_success);
    }

    public function testUserCreationForBlankPassword() {
        $randomNumber = rand(10, 10000);
        $user_details = array(
            'first_name' => 'SampleUser',
            'last_name' => 'SampleUser',
            'email' => 'sampleuser' . $randomNumber . '@samplemail.com',
            'password' => '',
        );
        $response_json = $this->create_user($user_details);
        $response_array = json_decode($response_json, true);

        $is_success = FALSE;
        if (isset($response_array['errors']) AND sizeof($response_array['errors']) > 0) {
            foreach ($response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.password_required')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }

        $this->assertTrue($is_success);
    }

    public function testUserLogin() {

        $userData = $this->getRandomUser();
        if ($userData != null) {

            $response_json = $this->executeLogin($userData->username, $userData->password);

            $response_array = json_decode($response_json, true);


            if (isset($response_array['success']) AND $response_array['success'] == TRUE) {
                $this->assertTrue(true);
            } else {
                $this->assertTrue(false);
            }
        }
    }

    public function testUserLoginInvalidCredentials() {



        $response_json = $this->executeLogin("invalidemail@mail.com", "wrongpass");

        $response_array = json_decode($response_json, true);


        $is_success = FALSE;
        if (isset($response_array['success']) AND $response_array['success'] == FALSE) {
            $errors_array = $response_array['errors'];
            foreach ($errors_array as $error) {

                if ($error['ERR_CODE'] == config('error_constants.login_invalid')) {
                    $is_success = TRUE;
                    break;
                }
            }
            $this->assertTrue($is_success);
        } else {
            $this->assertTrue($is_success);
        }
    }

    public function testUserList() {


        $token = $this->getValidToken();
        $url = $this->getApiUrl("/public/user");
        $curl = curl_init();
        $header = array("token:" . $token);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $users_response_json = curl_exec($curl);
        curl_close($curl);
        
        $users_response_array = json_decode($users_response_json, true);

        $is_success = FALSE;
        if (isset($users_response_array['success']) AND $users_response_array['success'] == TRUE
                AND sizeof($users_response_array['errors']) == 0) {
            $is_success = TRUE;
        }
        $this->assertTrue($is_success);
    }

    public function testFetchUserDetails() {
        $userData = $this->getRandomUser();
        $token = $this->getValidToken();
        $url = $this->getApiUrl("/public/user?uid=" . $userData->_id);
        $curl = curl_init();
        $header = array("token:" . $token);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, "uid=" . $userData->_id);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $user_response_json = curl_exec($curl);
        curl_close($curl);
        $user_response_array = json_decode($user_response_json, true);
        $is_success = FALSE;
        if (isset($user_response_array['success']) AND $user_response_array['success'] == TRUE
                AND sizeof($user_response_array['errors']) == 0) {
            $is_success = TRUE;
        }

        $this->assertTrue($is_success);
    }

    public function testFetchUserDetailsOfInvalidUser() {
        $userData = $this->getRandomUser();
        $token = $this->getValidToken();
//        Log::error($token);
        $url = $this->getApiUrl("/public/user?uid=" . "invalid_id");
        $curl = curl_init();
        $header = array("token:" . $token);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, "uid=" ."invalid_id");
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $user_response_json = curl_exec($curl);
        curl_close($curl);
//        Log::error("invalidusertest".$user_response_json);
        $user_response_array = json_decode($user_response_json, true);
        $is_success = FALSE;
        if (isset($user_response_array['success']) AND $user_response_array['success'] == FALSE
                AND sizeof($user_response_array['errors']) > 0) {
            $errors_array = $user_response_array['errors'];
            foreach ($errors_array as $error) {

                if ($error['ERR_CODE'] == config('error_constants.invalid_user_id')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }

        $this->assertTrue($is_success);
    }

    private function getValidToken() {
        $userData = $this->getRandomUser();
        $response_json = $this->executeLogin($userData->username, $userData->password);
        $response_array = json_decode($response_json, true);

        return $response_array['token'];
    }

    private function getRandomUser() {
        $usersModel = new UsersModel();
        $userData = $usersModel->get_random_user();
        return $userData;
    }

    private function executeLogin($username, $password) {

        $login_details = array("username" => $username,
            "password" => $password);
        $url = $this->getApiUrl("/public/login");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $login_details);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function stub_user() {

        $randomNumber = rand(10, 10000);

        $this->current_user_first_name = 'SampleUser';
        $this->current_user_last_name = 'SampleUser';
        $this->current_user_email = 'sampleuser' . $randomNumber . '@samplemail.com';
        $this->current_user_name = $this->current_user_email;
        $this->current_user_password = '123456';



        $response_array = array(
            'first_name' => $this->current_user_email,
            'last_name' => 'SampleUser',
            'email' => $this->current_user_email,
            'password' => $this->current_user_password,
        );
        return $response_array;
    }

    private function create_user($userDetailsArray) {
        $url = $this->getApiUrl("/public/register");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $userDetailsArray);
//        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function getApiUrl($path) {
        $path = ltrim($path, '/');
        $url = url("openpage-backend/" . $path);
        return $url;
    }

}
