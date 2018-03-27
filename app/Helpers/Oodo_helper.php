<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Oodo_helper {

    public static function token_generate() {
        $api_url = "https://54.202.141.25/api/auth/get_tokens";
        $data_array = array(
            "db" => "clouderp",
            "username" => "sysadmin@ilearners.hk",
            "password" => "sysadmin",
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_array));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($ch);
        return $result;
    }
    
    public function oodo_post($token,$data) {
        
    }

}
