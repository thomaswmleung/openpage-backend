<?php

namespace App\Http\Controllers;
use App\QuestionTypeModel;
use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;

class QuestionTypeController extends Controller {
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
            return response(json_encode($responseArray), 400);
        } else {

            QuestionTypeModel::create($question_type_array);
            $response_array = array("success" => TRUE, "errors" => array());
            return response(json_encode($response_array), 200);
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
            $rules['type']='required';
            $messages['type.required'] = config('error_constants.question_type_required');
        }
        if (isset($request->block) && $request->block != "") {
            $question_type_array['block'] = $request->block;
            $rules['type']='required';
            $messages['block.required'] = config('error_constants.block_required');
        }
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($question_type_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400);
        } else {
            $result = $this->update_question_type($question_type_array);
            if ($result) {
                return response("Question type updated successfully", 200);
            } else {
                $error['error'] = array("Something went wrong");
                return response(json_encode($error), 400);
            }
           
        }
    }
}
