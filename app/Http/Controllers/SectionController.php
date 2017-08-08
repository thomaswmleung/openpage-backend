<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SectionModel;

class SectionController extends Controller {

    public function section_list(Request $request) {
        $search_key = NULL;
        if (isset($request->search_key) && $request->search_key != "") {
            $search_key = $request->search_key;
        }
        $skip = NULL;
        if (isset($request->skip) && $request->skip != "") {
            $skip = $request->skip;
        }
        $limit = NULL;
        if (isset($request->limit) && $request->limit != "") {
            $limit = $request->limit;
        }
        $sectionModel = new SectionModel();
        if (isset($request->section_id) && $request->section_id != "") {
            $section_id = $request->section_id;

            $data_array = array(
                '_id' => $section_id
            );
            $section_details = $sectionModel->section_list($data_array, $search_key, $skip, $limit);
            if (!count($section_details)) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_section_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_section_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400);
            }
        } else {
            $section_details = $sectionModel->fetch_all_sections();
        }
        $response_array = array("success" => TRUE, "data" => $section_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }

}
