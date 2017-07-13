<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Emarref\Jwt\Claim\JwtId;
use Emarref\Jwt\Claim\JwtIdTest;
use Emarref\Jwt\Jwt;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Token;
use Emarref\Jwt\Encryption;
use Illuminate\Support\Facades\Cookie;
use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Verification\Context;

class Token_helper extends Controller {

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

//    function verify_token($serializedToken) {
//        $jwt = new Jwt();
//        $token = $serializedToken;
//        $de_token = $jwt->deserialize($token);
//
//        $secret = Config::get('constants.secret');
//        $algorithm = new Hs256($secret);
//        $encryption = Encryption\Factory::create($algorithm);
//
//        $context = new Context($encryption);
//        $result = $jwt->verify($de_token, $context);
//        return $result;
//    }
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

    function handle_token() {
        $response = new Response();
        $secret_key_error = false;
        $serializedToken = Cookie::get('token');
   
        if ($serializedToken == NULL) {
            //generate token
            $serializedToken = $this->generate_token();
            $cookie = Cookie::queue('token', $serializedToken, 1);
            $secret_key_error = false;
        } else {
            //verify token from cookie
            $verify_result = $this->verify_token($serializedToken);
            if (!$verify_result) {

                //generate token
                $serializedToken = $this->generate_token();
                $cookie = Cookie::queue('token', $serializedToken, 1);
                $secret_key_error = true;
            }
        }

        $return_data = array(
            'serializedToken' => $serializedToken,
            'secret_key_error' => $secret_key_error
        );

        return $return_data;
    }

}
