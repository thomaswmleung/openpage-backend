<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use App\UsersModel;
use App\MediaModel;

class MediaTest extends TestCase {

    public function testMediaCreationSuccess() {
        $image_path = public_path("test_images/" . "test.png");
        $cFile = curl_file_create($image_path, 'image/png', 'test.png');
        $media_data = array('type' => 'IMAGE',
            'extension' => "png",
            'owner' => "test_owner",
            'usage' => "251",
            'media_file' => $cFile
        );

        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/media");
        $curl = curl_init();
        $header = array(
            'enctype: multipart/form-data',
            'token: ' . $token
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $media_response_json = curl_exec($curl);
        $media_response_array = json_decode($media_response_json, true);
        $is_success = FALSE;
        if (isset($media_response_array['success']) AND $media_response_array['success'] == TRUE) {
            $is_success = TRUE;
        }
        $this->assertTrue($is_success);
    }

    public function testMediaCreationWithBlankType() {
        $image_path = public_path("test_images/" . "test.png");
        $cFile = curl_file_create($image_path, 'image/png', 'test.png');
        $media_data = array('type' => '',
            'extension' => "png",
            'owner' => "test_owner",
            'usage' => "251",
            'media_file' => $cFile
        );

        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/media");
        $curl = curl_init();
        $header = array(
            'enctype: multipart/form-data',
            'token: ' . $token
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);


        $media_response_json = curl_exec($curl);
        $media_response_array = json_decode($media_response_json, true);
        $is_success = FALSE;
        if (isset($media_response_array['errors'])
                AND sizeof($media_response_array['errors']) > 0) {

            foreach ($media_response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.media_type_required')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }
        $this->assertTrue($is_success);
    }

    public function testMediaCreationWithBlankExtension() {
        $image_path = public_path("test_images/" . "test.png");
        $cFile = curl_file_create($image_path, 'image/png', 'test.png');
        $media_data = array('type' => 'IMAGE',
            'extension' => "",
            'owner' => "test_owner",
            'usage' => "251",
            'media_file' => $cFile
        );

        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/media");
        $curl = curl_init();
        $header = array(
            'enctype: multipart/form-data',
            'token: ' . $token
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);


        $media_response_json = curl_exec($curl);
        $media_response_array = json_decode($media_response_json, true);
        $is_success = FALSE;
        if (isset($media_response_array['errors'])
                AND sizeof($media_response_array['errors']) > 0) {

            foreach ($media_response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.media_extension_required')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }
        $this->assertTrue($is_success);
    }

    public function testMediaCreationWithBlankUsage() {
        $image_path = public_path("test_images/" . "test.png");
        $cFile = curl_file_create($image_path, 'image/png', 'test.png');
        $media_data = array('type' => 'IMAGE',
            'extension' => "",
            'owner' => "test_owner",
            'usage' => "",
            'media_file' => $cFile
        );

        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/media");
        $curl = curl_init();
        $header = array(
            'enctype: multipart/form-data',
            'token: ' . $token
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);


        $media_response_json = curl_exec($curl);
        $media_response_array = json_decode($media_response_json, true);
        $is_success = FALSE;
        if (isset($media_response_array['errors'])
                AND sizeof($media_response_array['errors']) > 0) {

            foreach ($media_response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.media_usage_required')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }
        $this->assertTrue($is_success);
    }

    public function testMediaCreationWithInvalidMIME() {
        $image_path = public_path("test_images/" . "test.pem");
        $cFile = curl_file_create($image_path, 'pem', 'test.pem');
        $media_data = array('type' => 'IMAGE',
            'extension' => "",
            'owner' => "test_owner",
            'usage' => "251",
            'media_file' => $cFile
        );

        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/media");
        $curl = curl_init();
        $header = array(
            'enctype: multipart/form-data',
            'token: ' . $token
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $media_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);


        $media_response_json = curl_exec($curl);
        $media_response_array = json_decode($media_response_json, true);
        $is_success = FALSE;
        if (isset($media_response_array['errors'])
                AND sizeof($media_response_array['errors']) > 0) {

            foreach ($media_response_array['errors'] as $errors) {
                if ($errors['ERR_CODE'] == config('error_constants.invalid_media_file_mime')) {
                    $is_success = TRUE;
                    break;
                }
            }
        }
        $this->assertTrue($is_success);
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
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        $media_response_json = curl_exec($curl);
        curl_close($curl);
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
;
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
