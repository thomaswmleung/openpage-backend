<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\StaticHtmlPageModel;
use App\Helpers\Token_helper;
class StaticHtmlPageController extends Controller {
    /**
     * @SWG\Get(path="/static_html_page",
     *   tags={"Static HTML page"},
     *   summary="Returns list of static pages",
     *   description="Returns static pages data",
     *   operationId="static_html_page",
     *   produces={"application/json"},
     *   parameters={},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search parameter or key word to search",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="page_code",
     *     in="query",
     *     description="Filter by page code",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="reference_1",
     *     in="query",
     *     description="Filter by page reference 1",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="reference_2",
     *     in="query",
     *     description="Filter by page reference 2",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="reference_3",
     *     in="query",
     *     description="Filter by page reference 3",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="reference_4",
     *     in="query",
     *     description="Filter by page reference 4",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="reference_5",
     *     in="query",
     *     description="Filter by page reference 5",
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
     *   @SWG\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by value",
     *         type="array",
     *         @SWG\Items(
     *             type="string",
     *             enum={"page_code","reference_1", "reference_2", "reference_3", "reference_4", "reference_5"},
     *             default="created_at"
     *         ),
     *         collectionFormat="multi"
     *   ),
     *   @SWG\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Order by Ascending or descending",
     *         type="array",
     *         @SWG\Items(
     *             type="string",
     *             enum={"ASC", "DESC"},
     *             default="DESC"
     *         ),
     *         collectionFormat="multi"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   )
     * )
     */

    /**
     * @SWG\Get(path="/static_html_page/{_id}",
     *   tags={"Static HTML page"},
     *   summary="Returns Static page data",
     *   description="Returns Static page data",
     *   operationId="static_html_page",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the static html page that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid static html page id",
     *   )
     * )
     */
    public function static_html_page(Request $request) {
        $staticHtmlPageModel = new StaticHtmlPageModel();

        if (isset($request->_id) && $request->_id != "") {

            $static_html_page_id = $request->_id;
            $static_html_page_details = $staticHtmlPageModel->find_static_html_page_details($static_html_page_id);
            if ($static_html_page_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.static_html_page_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.static_html_page_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $static_html_page_details, "errors" => array());
                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
            }
        } else {
            $search_key = "";
            if (isset($request->search_key)) {
                $search_key = $request->search_key;
            }
            $page_code = "";
            if (isset($request->page_code)) {
                $page_code = $request->page_code;
            }
            $reference_1 = "";
            if (isset($request->reference_1)) {
                $reference_1 = $request->reference_1;
            }
            $reference_2 = "";
            if (isset($request->reference_2)) {
                $reference_2 = $request->reference_2;
            }
            $reference_3 = "";
            if (isset($request->reference_3)) {
                $reference_3 = $request->reference_3;
            }
            $reference_4 = "";
            if (isset($request->reference_4)) {
                $reference_4 = $request->reference_4;
            }
            $reference_5 = "";
            if (isset($request->reference_5)) {
                $reference_5 = $request->reference_5;
            }
      
            $skip = 0;
            if (isset($request->skip)) {
                $skip = (int) $request->skip;
            }
            $limit = config('constants.default_query_limit');
            if (isset($request->limit)) {
                $limit = (int) $request->limit;
            }
            $sort_by = 'created_at';
            if (isset($request->sort_by)) {
                $sort_by = $request->sort_by;
            }
            $order_by = 'DESC';
            if (isset($request->order_by)) {
                $order_by = $request->order_by;
            }
            $query_details = array(
                'search_key' => $search_key,
                'page_code' => $page_code,
                'reference_1' => $reference_1,
                'reference_2' => $reference_2,
                'reference_3' => $reference_3,
                'reference_4' => $reference_4,
                'reference_5' => $reference_5,
                'limit' => $limit,
                'skip' => $skip,
                'sort_by' => $sort_by,
                'order_by' => $order_by
            );

            $static_html_page_details = $staticHtmlPageModel->static_html_page_details($query_details);
            $total_count = $staticHtmlPageModel->total_count($query_details);
        }

        $response_array = array("success" => TRUE, "data" => $static_html_page_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/static_html_page",
     *   tags={"Static HTML page"},
     *   summary="Create a static_html_page",
     *   description="",
     *   operationId="add_or_update_static_html_page",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="query",
     *     name="page_code",
     *     description="Page code of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="content",
     *     description="Page content of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_1",
     *     description="Page reference_1 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_2",
     *     description="Page reference 2 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_3",
     *     description="Page reference 3 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_4",
     *     description="Page reference 4 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_5",
     *     description="Page reference 5 of the static page",
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
     * @SWG\Put(path="/static_html_page",
     *   tags={"Static HTML page"},
     *   summary="Update static page details",
     *   description="Update static page details",
     *   operationId="add_or_update_static_html_page",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="query",
     *     name="static_html_page_id",
     *     description="static page id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="query",
     *     name="page_code",
     *     description="Page code of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="content",
     *     description="Page content of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_1",
     *     description="Page reference 1 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_2",
     *     description="Page reference 2 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_3",
     *     description="Page reference 3 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_4",
     *     description="Page reference 4 of the static page",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="reference_5",
     *     description="Page reference 5 of the static page",
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
    public function add_or_update_static_html_page(Request $request) {
        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));
        $static_html_page_id = "";
        if (isset($request->static_html_page_id)) {
            $static_html_page_id = $request->static_html_page_id;
        }
        $page_code = "";
        if (isset($request->page_code)) {
            $page_code = $request->page_code;
        }
        $content = "";
        if (isset($request->content)) {
            $content = $request->content;
        }
        $reference_1 = "";
        if (isset($request->reference_1)) {
            $reference_1 = $request->reference_1;
        }
        $reference_2 = "";
        if (isset($request->reference_2)) {
            $reference_2 = $request->reference_2;
        }
        $reference_3 = "";
        if (isset($request->reference_3)) {
            $reference_3 = $request->reference_3;
        }
        $reference_4 = "";
        if (isset($request->reference_4)) {
            $reference_4 = $request->reference_4;
        }
        $reference_5 = "";
        if (isset($request->reference_5)) {
            $reference_5 = $request->reference_5;
        }

        $static_html_page_array = array(
            '_id' => $static_html_page_id,
            'page_code' => $page_code,
            'content' => $content,
            'reference_1' => $reference_1,
            'reference_2' => $reference_2,
            'reference_3' => $reference_3,
            'reference_4' => $reference_4,
            'reference_5' => $reference_5,
            'created_by'=>$user_id
        );

        if ($request->isMethod('post')) {
            $rules = array(
                'page_code' => 'required|unique:static_html_page,page_code',
                'content' => 'required'
            );
            $static_html_page_array['created_by']=$user_id;
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required|exists:static_html_page',
                'page_code' => 'required|unique:static_html_page,page_code,'.$static_html_page_id.',_id',
                'content' => 'required'
            );
            $static_html_page_array['updated_by']=$user_id;
        }
        $messages = [
            '_id.required' => config('error_constants.static_html_page_id_invalid'),
            'page_code.required' => config('error_constants.static_html_page_code_required'),
            'page_code.unique' => config('error_constants.static_html_page_code_unique'),
            'content.required' => config('error_constants.static_html_page_content_required'),
        ];
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($static_html_page_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $staticHtmlPageModel = new StaticHtmlPageModel();
        $static_page_id = $staticHtmlPageModel->create_or_update_static_html_page($static_html_page_array, $static_html_page_id);
        if ($request->isMethod('put')) {
            $success_msg = 'Static page updated successfully';
        } else {
            $success_msg = 'Static page created successfully';
        }
        $result_data = array(
            'id'=>$static_page_id,
            'message'=>$success_msg
        );
        $response_array = array("success" => TRUE, "data" => $result_data, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/static_html_page",
     *   tags={"Static HTML page"},
     *   summary="delete static page data",
     *   description="Delete static page from system",
     *   operationId="delete_static_html_page",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the static page that needs to be deleted",
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
    function delete_static_html_page(Request $request) {
        $static_html_page_id = trim($request->_id);
        $staticHtmlPageModel = new StaticHtmlPageModel();
        $static_html_page_data = $staticHtmlPageModel->find_static_html_page_details($static_html_page_id);
        if ($static_html_page_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.static_html_page_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.static_html_page_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        StaticHtmlPageModel::destroy($static_html_page_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
