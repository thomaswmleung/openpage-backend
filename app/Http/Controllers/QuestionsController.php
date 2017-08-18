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

            $questions_details = $questionsModel->question_list(NULL, NULL, NULL, NULL);
        }
        $response_array = array("success" => TRUE, "data" => $questions_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }

    public function add_or_update_question(Request $request) {
        $json_data = $request->getContent();
        $question = json_decode($json_data, true);
        if ($question == null) {
            return response(json_encode(array("error" => "Invalid Json")));
        }

        $question_number = $question['question_no'];
        $question_text = $question['question_text'];

        $question_type = $question['question_type'];
        $answer_col = $question['answer_cols'];

        $question_image_url = $question['image'];
        $answer_array = $question['answer'];



        $insert_data = array(
            'question_no' => $question_number,
            'answer_cols' => $answer_col,
            'question_text' => $question_text,
            'image' => $question_image_url,
            'answer' => $answer_array,
            'question_type' => $question_type,
            'x' => $question['x'],
            'y' => $question['y']
        );

        $question_id = "";
        if (isset($question['question_id']) AND $question['question_id'] != "") {
            $question_id = $question['question_id'];
        }

        $question_id = $this->create_or_update_question($insert_data, $question_id);


        $response_array['success'] = TRUE;

        return response(json_encode($response_array), 200);
    }

    function create_or_update_question($insert_data, $question_id) {
        $questionModel = new QuestionsModel();
        $questionDetails = $questionModel->add_questions($insert_data, $question_id);
        return $questionDetails->_id;
    }

    function delete_question(Request $request) {
        $question_id = trim($request->_id);

        $questionsModel = new QuestionsModel();
        $question_data = $questionsModel->get_question_details($question_id);
        if ($question_data == null) {
            $error['error'] = array("question not found");
            return response(json_encode($error), 400);
        }

        QuestionsModel::destroy($question_id);
        return response("question deleted successfully", 200);
    }

}
