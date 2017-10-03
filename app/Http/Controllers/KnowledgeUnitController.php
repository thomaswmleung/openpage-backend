<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\KnowledgeUnitModel;

class KnowledgeUnitController extends Controller {
    /**
     * @SWG\Get(path="/knowledge_unit",
     *   tags={"Knowledge Unit"},
     *   summary="Returns list of knowledge unit",
     *   description="Returns knowledge unit data",
     *   operationId="knowledge_unit_list",
     *   produces={"application/json"},
     *   parameters={},
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
     * @SWG\Get(path="/knowledge_unit/{_id}",
     *   tags={"Knowledge Unit"},
     *   summary="Returns knowledge unit flow data",
     *   description="Returns knowledge unit data",
     *   operationId="knowledge_unit_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the knowledge unit that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid knowledge unit id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function knowledge_unit_list(Request $request) {
        $knowledgeUnitModel = new KnowledgeUnitModel();

        if (isset($request->_id) && $request->_id != "") {

            $knowledge_unit_id = $request->_id;
            $knowledge_unit_details = $knowledgeUnitModel->find_knowledge_unit_details($knowledge_unit_id);
            if ($knowledge_unit_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.knowledge_unit_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.knowledge_unit_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $knowledge_unit_details, "errors" => array());
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

            $knowledge_unit_details = $knowledgeUnitModel->knowledge_unit_details($query_details);
            $total_count = $knowledgeUnitModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $knowledge_unit_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/knowledge_unit",
     *   tags={"Knowledge Unit"},
     *   summary="Create a knowledge_unit",
     *   description="",
     *   operationId="add_or_update_knowledge_unit",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Knowledge Unit json input",
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
     * @SWG\Put(path="/knowledge_unit",
     *   tags={"Knowledge Unit"},
     *   summary="Update knowledge_unit details",
     *   description="",
     *   operationId="add_or_update_knowledge_unit",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Knowledge Unit json input",
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
    public function add_or_update_knowledge_unit(Request $request) {
        $json_data = $request->getContent();
        $knowledge_unit_data_array = json_decode($json_data, true);

        if ($knowledge_unit_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }
        $knowledge_unit_id = "";
        if (isset($knowledge_unit_data_array['_id'])) {
            $knowledge_unit_id = $knowledge_unit_data_array['_id'];
        }
        $title = "";
        if (isset($knowledge_unit_data_array['title'])) {
            $title = $knowledge_unit_data_array['title'];
        }
        $code = "";
        if (isset($knowledge_unit_data_array['code'])) {
            $code = $knowledge_unit_data_array['code'];
        }
        

        $knowledge_unit_array = array(
            '_id' => $knowledge_unit_id,
            'title' => $title,
            'code' => $code
        );

        // validation : TODO
        if ($request->isMethod('post')) {
            $rules = array(
                'title' => 'required',
                'code' => 'required'
            );
            $messages = [
                'code.required' => config('error_constants.knowledge_unit_code_required'),
                'title.required' => config('error_constants.knowledge_unit_title_required'),
            ];
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required|exists:knowledge_unit',
                'title' => 'required',
                'code' => 'required',
            );
            $messages = [
                '_id.required' => config('error_constants.knowledge_unit_id_invalid'),
                '_id.exists' => config('error_constants.knowledge_unit_id_invalid'),
                'code.required' => config('error_constants.knowledge_unit_code_required'),
                'title.required' => config('error_constants.knowledge_unit_title_required'),
            ];
        }
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($knowledge_unit_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $knowledgeUnitModel = new KnowledgeUnitModel();
        $knowledgeUnitModel->create_or_update_knowledge_unit($knowledge_unit_array, $knowledge_unit_id);
        if ($knowledge_unit_id != "") {
            $success_msg = 'Knowledge Unit Updated Successfully';
        } else {
            $success_msg = 'Knowledge Unit Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/knowledge_unit",
     *   tags={"Knowledge Unit"},
     *   summary="delete knowledge_unit data",
     *   description="Delete knowledge unit from system",
     *   operationId="delete_knowledge_unit",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the knowledge_unit that needs to be deleted",
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
    function delete_knowledge_unit(Request $request) {
        $knowledge_unit_id = trim($request->_id);
        $knowledgeUnitModel = new KnowledgeUnitModel();
        $knowledge_unit_data = $knowledgeUnitModel->find_knowledge_unit_details($knowledge_unit_id);
        if ($knowledge_unit_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.knowledge_unit_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.knowledge_unit_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        KnowledgeUnitModel::destroy($knowledge_unit_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
