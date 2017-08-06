<?php


namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use App\UsersModel;
use App\PageGroupModel;

class PageGroupTest extends TestCase {
    
    public function testCreatePageGroupSuccess() {
        
        $json_data = file_get_contents(url("openpage-backend/public/pdf_page.json"));
//        
        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/page_group");
        $curl = curl_init();
        $header = array(
            "Content-Type: text/plain",
            'token: ' . $token
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $pagegroup_response_json = curl_exec($curl);
//        Log::error($pagegroup_response_json);
        $pagegroup_response_array = json_decode($pagegroup_response_json, true);
        $is_success = FALSE;
        if (isset($pagegroup_response_array['success']) AND $pagegroup_response_array['success'] == TRUE) {
            $is_success = TRUE;
        }
        $this->assertTrue($is_success);
    }
    public function testUpdatePageGroupSuccess() {
        
        $pageModel = new PageGroupModel();
        $randomPageGroup = $pageModel->getRandomDocument();
        $json_data = file_get_contents(url("openpage-backend/public/pdf_page.json"));
        
        $input_array = json_decode($json_data,TRUE);
        $input_array['_id'] = $randomPageGroup->_id;
        Log::error($input_array['_id'] );
        
        $input_json = json_encode($input_array);
        $token = $this->getValidToken();

        $url = $this->getApiUrl("/public/page_group");
        $curl = curl_init();
        $header = array(
            "Content-Type: text/plain",
            'token: ' . $token
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $input_json);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $pagegroup_response_json = curl_exec($curl);
        Log::error($pagegroup_response_json);
        $pagegroup_response_array = json_decode($pagegroup_response_json, true);
        $is_success = FALSE;
        if (isset($pagegroup_response_array['success']) AND $pagegroup_response_array['success'] == TRUE) {
            $is_success = TRUE;
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

        return curl_exec($curl);
    }

    private function getApiUrl($path) {
        $path = ltrim($path, '/');
        $url = url("openpage-backend/" . $path);
        return $url;
    }
}