<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Token_helper;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\ClassFlowModel;

class ClassFlowController extends Controller {
    /**
     * @SWG\Get(path="/class_flow",
     *   tags={"Class Flow"},
     *   summary="Returns list of class flow",
     *   description="Returns class flow data",
     *   operationId="class_flow_list",
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
     * @SWG\Get(path="/class_flow/{_id}",
     *   tags={"Class Flow"},
     *   summary="Returns class flow data",
     *   description="Returns class flow data",
     *   operationId="class_flow_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the class that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid class flow id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function class_flow_list(Request $request) {
        $classFlowModel = new ClassFlowModel();

        if (isset($request->_id) && $request->_id != "") {

            $class_flow_id = $request->_id;
            $class_flow_details = $classFlowModel->find_class_flow_details($class_flow_id);
            if ($class_flow_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.class_flow_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.class_flow_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $class_flow_details, "errors" => array());
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

            $class_flow_details = $classFlowModel->class_flow_details($query_details);
            $total_count = $classFlowModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $class_flow_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/class_flow",
     *   tags={"Class Flow"},
     *   summary="Create a class flow",
     *   description="",
     *   operationId="add_or_update_class_flow",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Class json input",
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
     * @SWG\Put(path="/class_flow",
     *   tags={"Class Flow"},
     *   summary="Update class flow details",
     *   description="",
     *   operationId="add_or_update_class_flow",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Class json input",
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
    public function add_or_update_class_flow(Request $request) {
        $json_data = $request->getContent();
        $class_flow_data_array = json_decode($json_data, true);

        if ($class_flow_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }
        $class_flow_id = "";
        if (isset($class_flow_data_array['_id'])) {
            $class_flow_id = $class_flow_data_array['_id'];
        }
        $title = "";
        if (isset($class_flow_data_array['title'])) {
            $title = $class_flow_data_array['title'];
        }
        $page_id = "";
        if (isset($class_flow_data_array['page_id'])) {
            $page_id = $class_flow_data_array['page_id'];
        }
        $resource_ids = "";
        if (isset($class_flow_data_array['resource_ids'])) {
            $resource_ids = $class_flow_data_array['resource_ids'];
        }

        $class_flow_array = array(
            '_id' => $class_flow_id,
            'title' => $title,
            'page_id' => $page_id,
            'resource_ids' => $resource_ids,
        );

        // validation : TODO
        if ($request->isMethod('post')) {
            $rules = array(
                'title' => 'required',
                'page_id' => 'required',
                'resource_ids' => 'required',
            );
            $messages = [
                'page_id.required' => config('error_constants.page_id_required'),
                'title.required' => config('error_constants.class_flow_title_required'),
                'resource_ids.required' => config('error_constants.class_flow_resource_required'),
            ];
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required',
                'title' => 'required',
                'page_id' => 'required',
                'resource_ids' => 'required',
            );
            $messages = [
                '_id.required' => config('error_constants.class_flow_id_invalid'),
                'page_id.required' => config('error_constants.page_id_required'),
                'title.required' => config('error_constants.class_flow_title_required'),
                'resource_ids.required' => config('error_constants.class_flow_resource_required'),
            ];
        }
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($class_flow_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $classFlowModel = new ClassFlowModel();
        $classFlowModel->create_or_update_class_flow($class_flow_array, $class_flow_id);
        if ($class_flow_id != "") {
            $success_msg = 'ClassFlow Updated Successfully';
        } else {
            $success_msg = 'ClassFlow Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/class_flow",
     *   tags={"Class Flow"},
     *   summary="delete class flow data",
     *   description="Delete class flow from system",
     *   operationId="delete_class flow",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the class_flow that needs to be deleted",
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
    function delete_class_flow(Request $request) {
        $class_flow_id = trim($request->_id);
        $classFlowModel = new ClassFlowModel();
        $class_flow_data = $classFlowModel->find_class_flow_details($class_flow_id);
        if ($class_flow_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.class_flow_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.class_flow_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        ClassFlowModel::destroy($class_flow_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
