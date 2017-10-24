<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\Helpers\ErrorMessageHelper;
use App\ResourceModel;

class ResourceController extends Controller {
    /**
     * @SWG\Get(path="/resource",
     *   tags={"Resource"},
     *   summary="Returns list of resource",
     *   description="Returns resource data",
     *   operationId="resource",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search based on remark and tags",
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
     * @SWG\Get(path="/resource/{_id}",
     *   tags={"Resource"},
     *   summary="Returns resource data",
     *   description="Returns resource data",
     *   operationId="resource",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the resource that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid resource id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function resource(Request $request) {
        $resourceModel = new ResourceModel();
        if (isset($request->_id) && $request->_id != "") {
            $resource_id = $request->_id;
            $resource_details = $resourceModel->resource_data($resource_id);
            if ($resource_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_media_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_media_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $resource_details, "errors" => array());
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

            $resource_details = $resourceModel->resource_details($query_details);
            $total_count = $resourceModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $resource_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/resource",
     *   tags={"Resource"},
     *   summary="Creating/Storing new resource ",
     *   description="Stores resource in the system",
     *   operationId="create_or_update_resource",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="title",
     *     in="query",
     *     description="Title of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="query",
     *     description="Description of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="resource_category_id",
     *     in="query",
     *     description="Resource category ID",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="url",
     *     in="query",
     *     description="URL of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="remark",
     *     in="query",
     *     description="Remark of the resource",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="tag[]",
     *     in="query",
     *     description="tags for the resource.",
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

    /**
     * @SWG\Put(path="/resource",
     *   tags={"Resource"},
     *   summary="Creating/Storing new resource ",
     *   description="Stores resource in the system",
     *   operationId="create_or_update_resource",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="Resource id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="title",
     *     in="query",
     *     description="Title of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="query",
     *     description="Description of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="resource_category_id",
     *     in="query",
     *     description="Resource category ID",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="url",
     *     in="query",
     *     description="URL of resource",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="remark",
     *     in="query",
     *     description="Remark of the resource",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="tag[]",
     *     in="query",
     *     description="tags for the resource.",
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
    public function create_or_update_resource(Request $request) {
        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));

        $resource_array = array(
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'resource_category_id' => $request->resource_category_id,
            'url' => $request->url,
            'remark' => $request->remark,
            'tag' => $request->tag,
            'created_by' => $user_id,
        );
        $rules = array(
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'resource_category_id' => 'required|exists:resource_category,_id',
            'url' => 'required',
            'created_by' => 'required|exists:users,_id',
        );
        $resource_id = "";
        if (isset($request->_id) && $request->_id != "") {
            $resource_id = $request->_id;
            $resource_array['_id'] = $resource_id;
            $rules['_id'] = 'required|exists:resource,_id';
        }
        $messages = [
            'title.required' => config('error_constants.resource_title_required'),
            'description.required' => config('error_constants.resource_description_required'),
            'type.required' => config('error_constants.resource_type_required'),
            'resource_category_id.required' => config('error_constants.resource_category_required'),
            'resource_category_id.exists' => config('error_constants.resource_category_exists'),
            'url.required' => config('error_constants.resource_url_required'),
            'created_by.required' => config('error_constants.media_created_by_required'),
            'created_by.exists' => config('error_constants.invalid_media_created_by'),
            '_id.required' => config('error_constants.resource_id_required'),
            '_id.exists' => config('error_constants.invalid_resource_id')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($resource_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            $resource_data = $this->add_or_update_resource($resource_array, $resource_id);
//            $resource_data = ResourceModel::create($resource_array);
            $response_array = array("success" => TRUE, "data" => $resource_data, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }

    function add_or_update_resource($resource_array, $resource_id) {
        $resourceModel = new ResourceModel();
        $resourceDetails = $resourceModel->add_or_edit_resource($resource_array, $resource_id);
        return $resourceDetails;
    }

    /**
     * @SWG\Delete(path="/resource",
     *   tags={"Resource"},
     *   summary="delete resource data",
     *   description="Delete resource from system",
     *   operationId="delete_resource",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the resource that needs to be deleted",
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
    function delete_resource(Request $request) {
        $resource_id = trim($request->_id);
        $resource_array = array(
            '_id' => $resource_id
        );
        $rules['_id'] = 'required|exists:resource,_id';
        $messages = [
            '_id.required' => config('error_constants.resource_id_required'),
            '_id.exists' => config('error_constants.invalid_resource_id')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($resource_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            ResourceModel::destroy($resource_id);
            $response_array = array("success" => TRUE);
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }

}
