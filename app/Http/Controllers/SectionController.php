<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SectionModel;
use App\QuestionsModel;

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

    public function add_or_update_section(Request $request) {
        $json_data = $request->getContent();
        $section = json_decode($json_data, true);
        if ($section == null) {
            return response(json_encode(array("error" => "Invalid Json")));
        }

        $questions_ids = array();
        $section_question_array = $section['question'];

        foreach ($section_question_array as $question) {

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


            array_push($questions_ids, $question_id);
        }



        $section_instruction_text = $section['instruction_text'];
        $section_type = $section['section_type'];
        $section_start_question_no = $section['start_question_no'];
        $section_with_sample_question = $section['with_sample_question'];
        $section_answer_cols = $section['answer_cols'];
        $section_suggestion_box = $section['suggestion_box'];
        $section_question = $questions_ids;

        $insert_data = array(
            'instruction_text' => $section_instruction_text,
            'section_type' => $section_type,
            'start_question_no' => $section_start_question_no,
            'with_sample_question' => $section_with_sample_question,
            'answer_cols' => $section_answer_cols,
            'suggestion_box' => $section_suggestion_box,
            'question' => $section_question
        );


        $section_id = "";
        if (isset($section['section_id']) && $section['section_id'] != "") {

            $section_id = $section['section_id'];
        }

        $section_id = $this->create_or_update_section($insert_data, $section_id);

        $response_array['success'] = TRUE;

        return response(json_encode($response_array), 200);
    }

    function create_or_update_question($insert_data, $question_id) {
        $questionModel = new QuestionsModel();
        $questionDetails = $questionModel->add_questions($insert_data, $question_id);
        return $questionDetails->_id;
    }

    function create_or_update_section($insert_data, $section_id) {
        $sectionModel = new SectionModel();
        $sectionDetails = $sectionModel->add_section($insert_data, $section_id);
        return $sectionDetails->_id;
    }

    function delete_section(Request $request) {
        $section_id = trim($request->_id);

        $sectionModel = new SectionModel();
        $section_data = $sectionModel->get_section_details($section_id);
        if ($section_data == null) {
            $error['error'] = array("section not found");
            return response(json_encode($error), 400);
        }
//        $data = explode("/", $page_group_data['preview_url']); // fetching file name from URL
//        $objectName = end($data);
//        $gcs_result = GCS_helper::delete_from_gcs($objectName);
//        if ($gcs_result) {
        SectionModel::destroy($section_id);
        return response("Section deleted successfully", 200);
//        } else {
//            $error['error'] = array("Something went wrong");
//            return response(json_encode($error), 400);
//        }
    }

}
