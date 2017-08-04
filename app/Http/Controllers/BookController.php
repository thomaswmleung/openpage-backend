<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller {

    public function create_book(Request $request) {
        Pdf_helper::generate_book("");
    }

}
