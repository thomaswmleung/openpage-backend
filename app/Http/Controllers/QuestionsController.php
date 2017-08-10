<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuestionsModel;

class QuestionsController extends Controller {

    public function question_list(Request $request) {
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
        $questionsModel = new QuestionsModel();
        if (isset($request->question_id) && $request->question_id != "") {
            $question_id = $request->question_id;

            $data_array = array(
                '_id' => $question_id
            );
            $questions_details = $questionsModel->question_list($data_array, $search_key, $skip, $limit);
            if (!count($questions_details)) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_question_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_question_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400);
            }
        } else {
            
            $questions_details = $questionsModel->fetch_all_questions();
        }
        $response_array = array("success" => TRUE, "data" => $questions_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }

}
