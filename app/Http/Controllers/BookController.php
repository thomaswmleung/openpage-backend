<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Curl_helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Helpers\tFPDF;
use App\QuestionsModel;
use App\SectionModel;
use App\MainModel;
use App\OverlayModel;
use App\BackgroundModel;
use App\PageModel;
use App\PageGroupModel;
use App\Helpers\Pdf_helper;

/*
 *  Class Name : PageGroupController
 *  Description : This controller handles parsing of JSON DATA and save to DB
 * 
 * 
 */

class BookController extends Controller {

  public function create_book(){
      Pdf_helper::generate_book("");
  }
}
