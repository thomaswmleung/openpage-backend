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
//use Codedge\Fpdf\Fpdf\Fpdf;
use App\Helpers\tFPDF;
use App\Helpers\Pdf_helper;

/*
 *  Class Name : PdfController
 *  Description : This controller handles pdf generation
 * 
 * 
 */

class PdfController extends Controller {

    // This function helps to generate PDF
    public function generate_pdf() {
        $json_data = file_get_contents(url('pdf_page.json'));
        $pdf_helper = new Pdf_helper();
        $result = $pdf_helper->generate_pdf_from_json($json_data);
        var_dump($result);
    }
}
