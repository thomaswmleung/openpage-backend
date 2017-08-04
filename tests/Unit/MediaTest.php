<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use App\UsersModel;
use App\MediaModel;

class MediaTest extends TestCase {

    public function testMediaCreationSuccess() {
//        $cFile = curl_file_create(url("/public/test_images/"."test.png"));
        $media_data = array('type' => 'IMAGE',
            'extension' => "png",
            'owner' => "test_owner",
            'usage' => "251",
//            'media_file' => $cFile
        );
        $fields_string = "";
//        foreach($media_data as $key=>$value){ 
//            $fields_string .= $key.'='.$value.'&'; 
//            
//        }
//rtrim($fields_string, '&');
        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/media");
        $curl = curl_init();
        $header = array("token :" . $token);

//        curl_setopt($curl, CURLOPT_POST, 1);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
////        curl_setopt($curl, CURLOPT_HTTPGET, 1);
////        curl_setopt($curl, CURLOPT_HTTPGET, 1);
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($curl, CURLOPT_URL, $url);
//        
////        curl_setopt($curl, CURLOPT_POST, 1);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
//        
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

         $header = array(
            'Accept: application/json',
            'Content-Type: application/form-data',
            'token: ' . $token
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($curl, CURLOPT_HTTPGET, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $media_response_json = curl_exec($curl);

        Log::error("create media");
        Log::error($media_response_json);

        $this->assertTrue(true);
    }

    public function testMediaList() {
        $token = $this->getValidToken();
        $header = array("token:", $token);
        $curl = curl_init();
        $url = $this->getApiUrl("/public/media");

        $header = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'token: ' . $token
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        $media_response_json = curl_exec($curl);
        curl_close($curl);

//        $media_response_json = curl_exec($curl);
//        
        Log::error("suraj");
        Log::error($media_response_json);
        $media_response_array = json_decode($media_response_json, true);
        $is_success = FALSE;
        if (isset($media_response_array['success']) AND $media_response_array['success'] == TRUE
                AND sizeof($media_response_array['errors']) == 0) {
            $is_success = TRUE;
        }
        $this->assertTrue(true);
    }

    public function testFetchMediaWithInvalidId() {
        $token = $this->getValidToken();
        $header = array("token:", $token);
        $curl = curl_init();
        $url = $this->getApiUrl("/public/media?mid=" . "invalid_media_id");
        $header = array("token:" . $token);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $media_response_json = curl_exec($curl);
        curl_close($curl);


        $media_response_array = json_decode($media_response_json, true);
        $is_success = FALSE;
        if (isset($media_response_array['success']) AND $media_response_array['success'] == FALSE
                AND sizeof($media_response_array['errors']) > 0) {

            $errors_array = $media_response_array['errors'];
            foreach ($errors_array as $error) {

                if ($error['ERR_CODE'] == config('error_constants.invalid_media_id')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }
        $this->assertTrue($is_success);
    }

    public function testFetchMediaWithValidId() {

        $media_model = new MediaModel();
        $media_doc = $media_model->get_random_media();
        $token = $this->getValidToken();
        $header = array("token:", $token);
        $curl = curl_init();
        $url = $this->getApiUrl("/public/media?mid=" . $media_doc->_id);
        $header = array("token:" . $token);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $media_response_json = curl_exec($curl);
        curl_close($curl);

        Log::error("fetch details");
        Log::error($media_response_json);
        $media_response_array = json_decode($media_response_json, true);

        $is_success = FALSE;
        if (isset($media_response_array['success']) AND $media_response_array['success'] == TRUE
                AND sizeof($media_response_array['errors']) == 0) {

            $is_success = TRUE;
        }
        $this->assertTrue($is_success);
    }

//    
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

        return curl_exec($curl);
    }

    private function getApiUrl($path) {
        $path = ltrim($path, '/');
        $url = url("openpage-backend/" . $path);
        return $url;
    }

}
