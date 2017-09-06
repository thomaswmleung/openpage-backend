<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SectionModel;
use App\QuestionsModel;

class SectionController extends Controller {
    /**
     * @SWG\Get(path="/section",
     *   tags={"Section"},
     *   summary="Returns list of section",
     *   description="Returns section data",
     *   operationId="section_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search parameter or key word to search",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="skip",
     *     in="query",
     *     description="this is offset or skip the records",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of records to be retrieved ",
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */

    /**
     * @SWG\Get(path="/section/{_id}",
     *   tags={"Section"},
     *   summary="Returns section data",
     *   description="Returns section data",
     *   operationId="section_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the section that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid section id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function section_list(Request $request) {

        $sectionModel = new SectionModel();
        if (isset($request->_id) && $request->_id != "") {
            $section_id = $request->_id;
            $section_details = $sectionModel->find_section_details($section_id);
            if ($section_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_section_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_section_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $section_details, "errors" => array());
                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
            }
        } else {
            $search_key = "";
            if (isset($request->search_key)) {
                $search_key = $request->search_key;
            }
            $skip = 0;
            if (isset($request->skip)) {
                $skip = (int) $request->skip;
            }
            $limit = config('constants.default_query_limit');
            if (isset($request->limit)) {
                $limit = (int) $request->limit;
            }
            $query_details = array(
                'search_key' => $search_key,
                'limit' => $limit,
                'skip' => $skip
            );

            $section_details = $sectionModel->section_list($query_details);
            $total_count = $sectionModel->total_count($search_key);
        }
        $response_array = array("success" => TRUE, "data" => $section_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/section",
     *   tags={"Section"},
     *   summary="Create a Section",
     *   description="",
     *   operationId="add_or_update_section",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="section json input",
     *     required=true,
     *     @SWG\Schema()
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Invalid data"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */

    /**
     * @SWG\Put(path="/section",
     *   tags={"Section"},
     *   summary="Update section details",
     *   description="",
     *   operationId="add_or_update_section",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Section json input",
     *     required=true,
     *     @SWG\Schema()
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Invalid data"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function add_or_update_section(Request $request) {
        $json_data = $request->getContent();
        $section = json_decode($json_data, true);
        if ($section == null) {
            return response(json_encode(array("success" => FALSE, "error" => "Invalid Json")))->header('Content-Type', 'application/json');
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

        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
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

    /**
     * @SWG\Delete(path="/section",
     *   tags={"Section"},
     *   summary="delete section data",
     *   description="Delete section from system",
     *   operationId="delete_section",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the section that needs to be deleted",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=400, description="Invalid data supplied"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    function delete_section(Request $request) {
        $section_id = trim($request->_id);
        $sectionModel = new SectionModel();
        $section_data = $sectionModel->get_section_details($section_id);
        if ($section_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_section_id')['error_code'],
                    "ERR_MSG" => config('error_constants.invalid_section_id')['error_message']));

            $response_array = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
        }
        SectionModel::destroy($section_id);
        $response_array = array("success" => TRUE);
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

}
