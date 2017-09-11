<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Token_helper;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\ClassModel;

class ClassController extends Controller {
    /**
     * @SWG\Get(path="/class",
     *   tags={"Class"},
     *   summary="Returns list of class",
     *   description="Returns class data",
     *   operationId="class_list",
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
     * @SWG\Get(path="/class/{_id}",
     *   tags={"Class"},
     *   summary="Returns class data",
     *   description="Returns class data",
     *   operationId="class_list",
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
     *     description="Invalid class id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function class_list(Request $request) {
        $classModel = new ClassModel();

        if (isset($request->_id) && $request->_id != "") {

            $class_id = $request->_id;
            $class_details = $classModel->find_class_details($class_id);
            if ($class_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.class_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.class_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $class_details, "errors" => array());
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

            $class_details = $classModel->class_details($query_details);
            $total_count = $classModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $class_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/class",
     *   tags={"Class"},
     *   summary="Create a class",
     *   description="",
     *   operationId="add_or_update_class",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="subject json input",
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
     * @SWG\Put(path="/class",
     *   tags={"Class"},
     *   summary="Update class details",
     *   description="",
     *   operationId="add_or_update_class",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Subject json input",
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
    public function add_or_update_class(Request $request) {
        $json_data = $request->getContent();
        $class_data_array = json_decode($json_data, true);

        if ($class_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }
        $class_id = "";
        if (isset($class_data_array['_id'])) {
            $class_id = $class_data_array['_id'];
        }
        $class_name = "";
        if (isset($class_data_array['class_name'])) {
            $class_name = $class_data_array['class_name'];
        }

        $class_array = array(
            '_id' => $class_id,
            'class_name' => $class_name,
        );

        // validation : TODO
        if ($request->isMethod('post')) {
            $rules = array(
                'class_name' => 'required|unique:class,class_name'
            );
            $messages = [
                'class_name.required' => config('error_constants.class_name_required'),
                'class_name.unique' => config('error_constants.class_name_unique'),
            ];
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required',
                'class_name' => 'required'
            );
            $messages = [
                '_id.required' => config('error_constants.class_id_invalid'),
                'class_name.required' => config('error_constants.class_name_required'),
                'class_name.exists' => config('error_constants.class_name_unique'),
            ];
        }
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($class_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $classModel = new ClassModel();
        $classModel->create_or_update_class($class_array, $class_id);
        if ($class_id != "") {
            $success_msg = 'Class Updated Successfully';
        } else {
            $success_msg = 'Class Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/class",
     *   tags={"Class"},
     *   summary="delete class data",
     *   description="Delete class from system",
     *   operationId="delete_class",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the class that needs to be deleted",
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
    function delete_class(Request $request) {
        $class_id = trim($request->_id);
        $classModel = new ClassModel();
        $class_data = $classModel->find_class_details($class_id);
        if ($class_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.class_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.class_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        ClassModel::destroy($class_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
