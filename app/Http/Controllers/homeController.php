<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AdminUserModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Curl_helper;
use App\Helpers\Language_helper;

class homeController extends Controller {

    public function getIndex() {
        $sessionInfo = Session::get('candidate_info');
      
     
         $info_array = array(
                'page_name' => 'Home',
            );
            return view('home', $info_array);
    }

 

}
