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
}