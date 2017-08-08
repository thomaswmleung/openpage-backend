<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Token_helper;
use App\BookModel;
use App\DomainModel;
use App\SubDomainModel;

class BookController extends Controller {

    public function create_book(Request $request) {
        // Pdf_helper::generate_book("");


        $book_raw_json_data = file_get_contents(url('book_json.json'));

        $book_data_array = json_decode($book_raw_json_data, true);
        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));

        // echo $user_id;exit;
        //echo "<pre>";        print_r($json_decode);
        $domain_ids = array();
        $sub_domain_ids = array();
        foreach ($book_data_array['syllabus']['domain'] as $domain) {

            $domain_insert_data = array(
                'code' => $domain['code'],
                'title' => $domain['title']
            );

            $domainModel = new DomainModel();
            $domain_id = $domainModel->create_domain($domain_insert_data);
            array_push($domain_ids, $domain_id);
        }

        foreach ($book_data_array['syllabus']['subdomain'] as $subdomain) {

            $subdomain_insert_data = array(
                'code' => $subdomain['code'],
                'title' => $subdomain['title']
            );

            $subDomainModel = new SubDomainModel();
            $sub_domain_id = $subDomainModel->create_sub_domain($subdomain_insert_data);
            array_push($sub_domain_ids, $sub_domain_id);
        }

        $book_data_array['syllabus']['domain'] = $domain_ids;
        $book_data_array['syllabus']['subdomain'] = $sub_domain_ids;

        if ($book_data_array['created_by'] == "") {
            $book_data_array['created_by'] = $user_id;
        }

        //echo "<pre>"; print_r($book_data_array['syllabus']); exit;

        $data_array = array(
            'page' => $book_data_array['page'],
            'toc' => $book_data_array['toc'],
            'syllabus' => $book_data_array['syllabus'],
            'organisation' => $book_data_array['organisation'],
            'created_by' => $book_data_array['created_by']
        );

        $bookModel = new BookModel();
        $result = $bookModel->create_book($data_array);

        if ($result != "") {
            $response_array = array("success" => TRUE, "data" => 'Book Created Successfully', "errors" => array());
            return response(json_encode($response_array), 200);
        } else {
            echo "Something went Wrong";
        }
    }

}
