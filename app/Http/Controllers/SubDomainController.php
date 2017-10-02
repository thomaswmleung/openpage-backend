<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\SubDomainModel;

class SubDomainController extends Controller {
    /**
     * @SWG\Get(path="/sub_domain",
     *   tags={"Sub Domain"},
     *   summary="Returns list of sub domain",
     *   description="Returns sub domain data",
     *   operationId="sub_domain_list",
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
     * @SWG\Get(path="/sub_domain/{_id}",
     *   tags={"Sub Domain"},
     *   summary="Returns sub_domain flow data",
     *   description="Returns sub domain data",
     *   operationId="sub_domain_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the sub domain that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid sub domain id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function sub_domain_list(Request $request) {
        $subDomainModel = new SubDomainModel();

        if (isset($request->_id) && $request->_id != "") {

            $sub_domain_id = $request->_id;
            $sub_domain_details = $subDomainModel->find_sub_domain_details($sub_domain_id);
            if ($sub_domain_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.sub_domain_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.sub_domain_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $sub_domain_details, "errors" => array());
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

            $sub_domain_details = $subDomainModel->sub_domain_details($query_details);
            $total_count = $subDomainModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $sub_domain_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/sub_domain",
     *   tags={"Sub Domain"},
     *   summary="Create a sub domain",
     *   description="",
     *   operationId="add_or_update_sub_domain",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Sub domain json input",
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
     * @SWG\Put(path="/sub_domain",
     *   tags={"Sub Domain"},
     *   summary="Update sub domain details",
     *   description="",
     *   operationId="add_or_update_sub_domain",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Sub Domain json input",
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
    public function add_or_update_sub_domain(Request $request) {
        $json_data = $request->getContent();
        $sub_domain_data_array = json_decode($json_data, true);

        if ($sub_domain_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }
        $sub_domain_id = "";
        if (isset($sub_domain_data_array['_id'])) {
            $sub_domain_id = $sub_domain_data_array['_id'];
        }
        $title = "";
        if (isset($sub_domain_data_array['title'])) {
            $title = $sub_domain_data_array['title'];
        }
        $code = "";
        if (isset($sub_domain_data_array['code'])) {
            $code = $sub_domain_data_array['code'];
        }
        

        $sub_domain_array = array(
            '_id' => $sub_domain_id,
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
                'code.required' => config('error_constants.sub_code_required'),
                'title.required' => config('error_constants.sub_domain_title_required'),
            ];
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required|exists:sub_domain',
                'title' => 'required',
                'code' => 'required',
            );
            $messages = [
                '_id.required' => config('error_constants.sub_domain_id_invalid'),
                '_id.exists' => config('error_constants.sub_domain_id_invalid'),
                'code.required' => config('error_constants.sub_code_required'),
                'title.required' => config('error_constants.sub_domain_title_required'),
            ];
        }
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($sub_domain_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $subDomainModel = new SubDomainModel();
        $subDomainModel->create_or_update_sub_domain($sub_domain_array, $sub_domain_id);
        if ($sub_domain_id != "") {
            $success_msg = 'Sub Domain Updated Successfully';
        } else {
            $success_msg = 'Sub Domain Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/sub_domain",
     *   tags={"Sub Domain"},
     *   summary="delete sub domain data",
     *   description="Delete sub domain from system",
     *   operationId="delete_sub_domain",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the sub domain that needs to be deleted",
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
    function delete_sub_domain(Request $request) {
        $sub_domain_id = trim($request->_id);
        $subDomainModel = new SubDomainModel();
        $sub_domain_data = $subDomainModel->find_sub_domain_details($sub_domain_id);
        if ($sub_domain_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.sub_domain_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.sub_domain_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        SubDomainModel::destroy($sub_domain_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
