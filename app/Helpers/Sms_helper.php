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
use Illuminate\Support\Facades\Log;

class Sms_helper extends Controller {

    function send_sms($username, $password, $sender_id, $mobile_number, $message, $priority) {
//        $data = "";
        $priority = 'sdnd';
        $phone_no = "";
        if (strlen($mobile_number) > 10) {
            $phone_no = substr($mobile_number, -10);
        } else {
            $phone_no = $mobile_number;
        }
        $url = "http://bhashsms.com/api/sendmsg.php?user=" . $username . "&pass=" . $password . "&sender=" . $sender_id . "&phone=" . $phone_no . "&text=" . $message . "&priority=" . $priority . "&stype=normal";
        $data = file_get_contents($url);
        Log::error($url);
        return $data;
    }

    function send_multiple_sms($username, $password, $sender_id, $mobile_number, $message, $priority) {
//        $data = "";
        $priority = 'sdnd';
        $phone_no = "";
        if (strlen($mobile_number) > 10) {
            $phone_no = substr($mobile_number, -10);
        } else {
            $phone_no = $mobile_number;
        }
        $url = "http://bhashsms.com/api/sendmsg.php?user=" . $username . "&pass=" . $password . "&sender=" . $sender_id . "&phone=" . $phone_no . "&text=" . $message . "&priority=" . $priority . "&stype=normal";
        $data = file_get_contents($url);
        return $data;
    }

    function credits_remaining($username, $password) {
        $url = "http://bhashsms.com/api/checkbalance.php?user=" . $username . "&pass=" . $password;
        $credits_remaining = file_get_contents($url);
        return $credits_remaining;
    }

}
