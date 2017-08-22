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
     *   parameters={},
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
        if (isset($request->_id) && $request->_id != "") {
            $question_id = $request->_id;

            $data_array = array(
                '_id' => $question_id
            );
            $questions_details = $questionsModel->question_details($data_array);
            if (!count($questions_details)) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_question_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_question_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400);
            }
        } else {

            $questions_details = $questionsModel->question_details();
        }
        $response_array = array("success" => TRUE, "data" => $questions_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }

    /**
     * @SWG\Get(path="/question_search",
     *   tags={"Question"},
     *   summary="Returns question data based on search keyword",
     *   description="Returns question data",
     *   operationId="question_search",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search keyword that needs to be searched in question",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid data",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function question_search(Request $request) {
        $search_key = $request->search_key;
//      $skip = NULL;
        $skip = 0;
        if (isset($request->skip) && $request->skip != "") {
            $skip = $request->skip;
        }
//      $limit = NULL;
        $limit = 100;
        if (isset($request->limit) && $request->limit != "") {
            $limit = $request->limit;
        }
        $data_array = array(
            'search_key' => $search_key,
            'skip' => $skip,
            'limit' => $limit
        );
        $questionsModel = new QuestionsModel();
        $question_details = $questionsModel->question_search($data_array);
        
        $response_array = array("success" => TRUE, "data" => $question_details, "errors" => array());
        return response(json_encode($response_array), 200);
     
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
            $error['error'] = array("question not found");
            return response(json_encode($error), 400);
        }

        QuestionsModel::destroy($question_id);
        return response("question deleted successfully", 200);
    }

}
