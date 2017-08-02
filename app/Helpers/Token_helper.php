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

        $current_time = date('Y-m-d H:i');
        $token->addClaim(new Claim\PrivateClaim('timestamp', $current_time));

        $jwt = new Jwt();
        $secret = Config::get('constants.secret');
        $algorithm = new Hs256($secret);
        $encryption = Encryption\Factory::create($algorithm);
        $serializedToken = $jwt->serialize($token, $encryption);
        return $serializedToken;
    }

    /*  */

    function generate_user_token($uid) {
        $token = new Token();
        $token->addClaim(new Claim\IssuedAt());

        $current_time = date('Y-m-d H:i');
//        $token->addClaim(new Claim\PrivateClaim('timestamp', $current_time));
        $token->addClaim(new Claim\PrivateClaim('data', $current_time . "||" . $uid));
        $jwt = new Jwt();
        $secret = Config::get('constants.secret');
        $algorithm = new Hs256($secret);
        $encryption = Encryption\Factory::create($algorithm);
        $serializedToken = $jwt->serialize($token, $encryption);
        return $serializedToken;
    }

    public function verify_token($token) {
        try {
        $jwt = new Jwt();
        $de_token = $jwt->deserialize($token);

        $secret = Config::get('constants.secret');
        $algorithm = new Hs256($secret);
        $encryption = Encryption\Factory::create($algorithm);

        $context = new Context($encryption);
        
            $verify_result = $jwt->verify($de_token, $context);
        } catch (VerificationException $e) {
            $verify_result = FALSE;
        }
        catch (\Exception $e){
            $verify_result = FALSE;
        }
        return $verify_result;
    }

    function token_decode($serializedToken) {
        $jwt = new Jwt();
        $deserializedToken = $jwt->deserialize($serializedToken);

        $header = $deserializedToken->getHeader()->jsonSerialize();
        $playload = $deserializedToken->getPayload()->jsonSerialize();
        return $playload;
    }

    static function fetch_user_id_from_token($serializedToken) {
        $jwt = new Jwt();
        $deserializedToken = $jwt->deserialize($serializedToken);

        $header = $deserializedToken->getHeader()->jsonSerialize();
        $playload = $deserializedToken->getPayload()->jsonSerialize();

        $token_string = json_decode($playload);
        $token_data = explode("||", $token_string->data);        
        $user_id = $token_data[1];
        return $user_id;
    }

}
