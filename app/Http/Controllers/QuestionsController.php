<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuestionsModel;

class QuestionsController extends Controller {
    /**
     * @SWG\Get(path="/question",
     *   tags={"Question"},
     *   summary="Returns list of question",
     *   description="Returns question data",
     *   operationId="question_list",
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
     * @SWG\Get(path="/question/{_id}",
     *   tags={"Question"},
     *   summary="Returns question data",
     *   description="Returns question data",
     *   operationId="question_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the question that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid question id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function question_list(Request $request) {
        $questionsModel = new QuestionsModel();
        if (isset($request->_id) && $request->_id != "") {
            $question_id = $request->_id;
            $questions_details = $questionsModel->find_question_details($question_id);
            if ($questions_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_question_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_question_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $questions_details, "errors" => array());
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

            $questions_details = $questionsModel->question_details($query_details);
            $total_count = $questionsModel->total_count($search_key);
        }
        $response_array = array("success" => TRUE, "data" => $questions_details,  "total_count" => $total_count,"errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/question",
     *   tags={"Question"},
     *   summary="Create a question",
     *   description="",
     *   operationId="add_or_update_question",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="question json input",
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
     * @SWG\Put(path="/question",
     *   tags={"Question"},
     *   summary="Update question details",
     *   description="",
     *   operationId="add_or_update_question",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="question json input",
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
    public function add_or_update_question(Request $request) {
        $json_data = $request->getContent();
        $question = json_decode($json_data, true);
        if ($question == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
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

        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    function create_or_update_question($insert_data, $question_id) {
        $questionModel = new QuestionsModel();
        $questionDetails = $questionModel->add_questions($insert_data, $question_id);
        return $questionDetails->_id;
    }

    /**
     * @SWG\Delete(path="/question",
     *   tags={"Question"},
     *   summary="delete question data",
     *   description="Delete question from system",
     *   operationId="delete_question",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the question that needs to be deleted",
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
    function delete_question(Request $request) {
        $question_id = trim($request->_id);

        $questionsModel = new QuestionsModel();
        $question_data = $questionsModel->get_question_details($question_id);
        if ($question_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_question_id')['error_code'],
                    "ERR_MSG" => config('error_constants.invalid_question_id')['error_message']));

            $response_array = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
        }

        QuestionsModel::destroy($question_id);
        $response_array = array("success" => TRUE);
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

}
