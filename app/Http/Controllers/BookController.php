<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Pdf_helper;

class BookController extends Controller {

    public function create_book(Request $request) {
        Pdf_helper::generate_book("");
    }

}
