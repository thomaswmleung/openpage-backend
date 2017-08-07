<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PageModel;

class PageController extends Controller {

    public function page_list(Request $request) {
        $search_key=NULL;
        if (isset($request->search_key) && $request->search_key != "") {
            $search_key = $request->search_key;
        }
        $skip=NULL;
        if (isset($request->skip) && $request->skip != "") {
            $skip = $request->skip;
        }
        $limit=NULL;
        if (isset($request->limit) && $request->limit != "") {
            $limit = $request->limit;
        }
        $pageModel = new PageModel();
        if (isset($request->page_id) && $request->page_id != "") {
            $page_id = $request->page_id;

            $data_array = array(
                '_id' => $page_id
            );
            $page_details = $pageModel->page_list($data_array,$search_key,$skip,$limit);
            if ($page_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_page_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_page_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400);
            }
        } else {
            $page_details = $pageModel->page_list();
        }
        $response_array = array("success" => TRUE, "data" => $page_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }

}
