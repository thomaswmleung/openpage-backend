<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\ResourceCategoryModel;

class ResourceCategoryController extends Controller {
    /**
     * @SWG\Get(path="/resource_category",
     *   tags={"Resource Category"},
     *   summary="Returns list of resource category",
     *   description="Returns resource category data",
     *   operationId="resource_category_list",
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
     * @SWG\Get(path="/resource_category/{_id}",
     *   tags={"Resource Category"},
     *   summary="Returns resource_category data",
     *   description="Returns resource_category data",
     *   operationId="resource_category_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the resource_category that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid resource_category id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function resource_category_list(Request $request) {
        $resourceCategoryModel = new ResourceCategoryModel();

        if (isset($request->_id) && $request->_id != "") {

            $resource_category_id = $request->_id;
            $resource_category_details = $resourceCategoryModel->find_resource_category_details($resource_category_id);
            if ($resource_category_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.resource_category_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.resource_category_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $resource_category_details, "errors" => array());
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

            $resource_category_details = $resourceCategoryModel->resource_category_details($query_details);
            $total_count = $resourceCategoryModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $resource_category_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/resource_category",
     *   tags={"Resource Category"},
     *   summary="Create a resource_category",
     *   description="",
     *   operationId="add_or_update_resource_category",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="query",
     *     name="resource_category",
     *     description="Resource category  input",
     *     required=true,
     *     type="string"
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
     * @SWG\Put(path="/resource_category",
     *   tags={"Resource Category"},
     *   summary="Update resource category details",
     *   description="",
     *   operationId="add_or_update_resource_category",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="query",
     *     name="resource_category_id",
     *     description="Resource category id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="query",
     *     name="resource_category",
     *     description="Resource category name",
     *     required=true,
     *     type="string"
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
    public function add_or_update_resource_category(Request $request) {

        $resource_category_id = "";
        if (isset($request->resource_category_id)) {
            $resource_category_id = $request->resource_category_id;
        }
        $resource_category_name = "";
        if (isset($request->resource_category)) {
            $resource_category_name = $request->resource_category;
        }

        $resource_category_array = array(
            '_id' => $resource_category_id,
            'resource_category' => $resource_category_name,
        );

        if ($request->isMethod('post')) {
            $rules = array(
                'resource_category' => 'required|unique:resource_category,resource_category'
            );
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required|exists:resource_category',
                'resource_category' => 'required|unique:resource_category,resource_category,'.$resource_category_id.',_id'
            );
        }
        $messages = [
            '_id.required' => config('error_constants.resource_category_id_invalid'),
            'resource_category.required' => config('error_constants.resource_category_name_required'),
            'resource_category.unique' => config('error_constants.resource_category_name_unique'),
        ];
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($resource_category_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $resourceCategoryModel = new ResourceCategoryModel();
        $category_resource_id = $resourceCategoryModel->create_or_update_resource_category($resource_category_array, $resource_category_id);
        if ($resource_category_id != "") {
            $success_msg = 'Resource Category Updated Successfully';
        } else {
            $success_msg = 'Resource Category Created Successfully';
        }
        $result_data = array(
            'id'=>$category_resource_id,
            'message'=>$success_msg
        );
        $response_array = array("success" => TRUE, "data" => $result_data, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/resource_category",
     *   tags={"Resource Category"},
     *   summary="delete resource category data",
     *   description="Delete resource category from system",
     *   operationId="delete_resource_category",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the resource category that needs to be deleted",
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
    function delete_resource_category(Request $request) {
        $resource_category_id = trim($request->_id);
        $resourceCategoryModel = new ResourceCategoryModel();
        $resource_category_data = $resourceCategoryModel->find_resource_category_details($resource_category_id);
        if ($resource_category_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.resource_category_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.resource_category_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        ResourceCategoryModel::destroy($resource_category_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
