<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Token_helper;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\LayoutModel;

class LayoutController extends Controller {
    /**
     * @SWG\Get(path="/layout",
     *   tags={"Layout"},
     *   summary="Returns list of layout",
     *   description="Returns layout data",
     *   operationId="layout_list",
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
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of records to be retrieved ",
     *     type="string"
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
     * @SWG\Get(path="/layout/{_id}",
     *   tags={"Layout"},
     *   summary="Returns layout data",
     *   description="Returns layout data",
     *   operationId="layout_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the layout that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid layout id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function layout_list(Request $request) {
        $layoutModel = new LayoutModel();
        if (isset($request->_id) && $request->_id != "") {
            $layout_id = $request->_id;

            $layout_details = $layoutModel->find_layout_details($layout_id);
            if ($layout_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_layout_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_layout_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
        } else {
            $search_key = "";
            if (isset($request->search_key)) {
                $search_key = $request->search_key;
            }
            $skip = 0;
            if (isset($request->skip)) {
                $skip = $request->skip;
            }
            $limit = config('constants.default_query_limit');
            if (isset($request->limit)) {
                $search_key = $request->limit;
            }
            $query_details = array(
                'search_key' => $search_key,
                'limit' => $limit,
                'skip' => $skip
            );

            $layout_details = $layoutModel->layout_details($query_details);
            $total_count = $layoutModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $layout_details,"total_count"=>$total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/layout",
     *   tags={"Layout"},
     *   summary="Create a layout",
     *   description="",
     *   operationId="add_or_update_layout",
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
     * @SWG\Put(path="/layout",
     *   tags={"Layout"},
     *   summary="Update layout details",
     *   description="",
     *   operationId="add_or_update_layout",
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
    public function add_or_update_layout(Request $request) {
        $json_data = $request->getContent();
        $layout_data_array = json_decode($json_data, true);

        if ($layout_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }

        $layout_array = array(
            '_id' => $layout_data_array['_id'],
            'layout_code' => $layout_data_array['layout_code'],
            'overlay' => $layout_data_array['overlay'],
            'main' => $layout_data_array['main'],
            'background' => $layout_data_array['background'],
            'front_cover' => $layout_data_array['front_cover'],
            'table_of_content' => $layout_data_array['table_of_content'],
            'back_cover' => $layout_data_array['back_cover'],
        );
        // validation : TODO

        $layout_id = $layout_data_array['_id'];
        $layoutModel = new LayoutModel();
        $layoutModel->create_or_update_layout($layout_array, $layout_id);
        if ($layout_id != "") {
            $success_msg = 'Layout Updated Successfully';
        } else {
            $success_msg = 'Layout Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/layout",
     *   tags={"Layout"},
     *   summary="delete layout data",
     *   description="Delete layout from system",
     *   operationId="delete_layout",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the layout that needs to be deleted",
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
    function delete_layout(Request $request) {
        $layout_id = trim($request->_id);
        $layoutModel = new LayoutModel();
        $layout_data = $layoutModel->layout_details(array('_id' => $layout_id));
        if ($layout_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_layout_id'),
                    "ERR_MSG" => config('error_messages' . "." .
                            config('error_constants.invalid_layout_id'))));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        LayoutModel::destroy($layout_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
