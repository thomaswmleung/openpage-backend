<?php

namespace App\Helpers;

use Carbon\Carbon;
use Emarref\Jwt\Encryption;

class Curl_helper {

    public static function setup_curl($data, $url, $image = null) {
        //check token in the cookie
        $token_helper = new Token_helper();
        $serializedToken = $token_helper->handle_token();

        if ($serializedToken['secret_key_error']) {
            $return_data['is_token_error'] = true;
            return $return_data;
        } else {
            $data['token'] = $serializedToken['serializedToken'];
            $data['type'] = 'WEB';

            $data_e = array(
                'data' => json_encode($data)
            );
            if ($image != NULL) {
                $data_e['image'] = $image;
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_e);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            curl_close($curl);
            return $result;
        }
    }

}
