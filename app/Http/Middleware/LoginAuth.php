<?php

namespace App\Http\Middleware;

use Redirect;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Helpers\Curl_helper;

class LoginAuth {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $value = session('user_info');

        if ($value == NULL) {
            return Redirect::to('login');
        }
        return $next($request);
    }

}
