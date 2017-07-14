<?php

namespace App\Http\Middleware;

use Redirect;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Helpers\Curl_helper;
use App\Helpers\Token_helper;
use DateTime;
use DateInterval;

class JwtAuth {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $value = session('user_info');
        $jwt_token = $value['jwt_token'];

        $token_helper = new Token_helper();
        $set_time = $token_helper->token_decode($jwt_token);
        $set_time = json_decode($set_time, true);
        $t = $set_time['user'];

        $minutes_to_add = 30;
        $time = new DateTime($t);
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $expiration_time = $time->format('Y-m-d H:i');
        $current_time = date('Y-m-d H:i');


        if ($current_time > $expiration_time) {
            return Redirect::to('login');
        }

       
        return $next($request);
    }

}
