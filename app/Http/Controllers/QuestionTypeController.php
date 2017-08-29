<?php

namespace App\Http\Controllers;

use App\QuestionTypeModel;
use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;

class QuestionTypeController extends Controller {
    /**
     * @SWG\Get(path="/question_type",
     * tags={"Question Type"},
     *   summary="Returns list of question types",
     *   description="Returns question types data",
     *   operationId="question_type",
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
     * @SWG\Get(path="/question_type/{_id}",
     * tags={"Question Type"},
     *   summary="Returns question type data",
     *   description="Returns question types data",
     *   operationId="question_type",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the question type that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid question type id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function question_type(Request $request) {
        $questionTypeModel = new QuestionTypeModel();
        if (isset($request->_id)) {


            $question_type_id = $request->_id;
            // get user details
            $data_array = array(
                '_id' => $question_type_id
            );
            $question_type_details = $questionTypeModel->question_type_details($data_array);
            if ($question_type_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_question_type_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_question_type_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
        } else {


            $question_type_details = $questionTypeModel->question_type_details();
        }

        $response_array = array("success" => TRUE, "data" => $question_type_details, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/question_type",
     *   tags={"Question Type"},
     *   summary="Creating/Storing new question types",
     *   description="Creation of question types",
     *   operationId="create_question_type",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of the question",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="block[]",
     *     in="query",
     *     description="The question types",
     *     required=true,
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
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
    public function create_question_type(Request $request) {
        $question_type_array = array(
            'type' => $request->type,
            'block' => $request->block,
        );
        $rules = array(
            'type' => 'required',
            'block' => 'required',
        );
        $messages = [
            'type.required' => config('error_constants.question_type_required'),
            'block.required' => config('error_constants.block_required'),
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($question_type_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {

            QuestionTypeModel::create($question_type_array);
            $response_array = array("success" => TRUE, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }

    /**
     * @SWG\Put(path="/question_type",
     *   tags={"Question Type"},
     *   summary="Updating question types",
     *   description="Updating of question types",
     *   operationId="update_question_type",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="Id of the question type",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of question",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="block[]",
     *     in="query",
     *     description="The question types",
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
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
    public function update_question_type(Request $request) {

        $question_type_array = array(
            '_id' => $request->_id,
        );
        $rules = array(
            '_id' => 'required|exists:question_type,_id',
        );
        $messages = [
            '_id.required' => config('error_constants.question_type_id_required'),
            'exists.required' => config('error_constants.invalid_question_type_id'),
        ];
        if (isset($request->type) && $request->type != "") {
            $question_type_array['type'] = $request->type;
            $rules['type'] = 'required';
            $messages['type.required'] = config('error_constants.question_type_required');
        }
        if (isset($request->block) && $request->block != null) {
            $question_type_array['block'] = $request->block;
            $rules['block'] = 'required';
            $messages['block.required'] = config('error_constants.block_required');
        }
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);
        $validator = Validator::make($question_type_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            $result = $this->edit_question_type($question_type_array);
            if ($result) {
                $responseArray = array("success" => TRUE);
                return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
            } else {
                $responseArray = array("success" => FALSE, "errors" => "Something went wrong");
                return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
            }
        }
    }

    public function edit_question_type($data) {
        $questionTypeModel = new QuestionTypeModel();
        return $questionTypeModel->update_question_type($data);
    }

    /**
     * @SWG\Delete(path="/question_type",
     *   tags={"Question Type"},
     *   summary="delete Question Type data",
     *   description="Delete Question Type in the system",
     *   operationId="delete_question_type",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the question type that needs to be deleted",
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
    public function delete_question_type(Request $request) {

        $question_type_id = trim($request->_id);
        $question_type_array = array(
            '_id' => $question_type_id,
        );
        $rules = array(
            '_id' => 'required|exists:question_type,_id',
        );
        $messages = [
            '_id.required' => config('error_constants.question_type_id_required'),
            '_id.exists' => config('error_constants.invalid_question_type_id'),
        ];
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);
        $validator = Validator::make($question_type_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            QuestionTypeModel::destroy($question_type_id);
            $responseArray = array("success" => TRUE);
            return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
        }
        
        
     
    }

}
