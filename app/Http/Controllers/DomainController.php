<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\DomainModel;

class DomainController extends Controller {
    /**
     * @SWG\Get(path="/domain",
     *   tags={"Domain"},
     *   summary="Returns list of domain",
     *   description="Returns domain data",
     *   operationId="domain_list",
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
     * @SWG\Get(path="/domain/{_id}",
     *   tags={"Domain"},
     *   summary="Returns domain flow data",
     *   description="Returns domain data",
     *   operationId="domain_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the domain that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid domain id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function domain_list(Request $request) {
        $domainModel = new DomainModel();

        if (isset($request->_id) && $request->_id != "") {

            $domain_id = $request->_id;
            $domain_details = $domainModel->find_domain_details($domain_id);
            if ($domain_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.domain_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.domain_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $domain_details, "errors" => array());
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

            $domain_details = $domainModel->domain_details($query_details);
            $total_count = $domainModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $domain_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/domain",
     *   tags={"Domain"},
     *   summary="Create a domain",
     *   description="",
     *   operationId="add_or_update_domain",
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
     * @SWG\Put(path="/domain",
     *   tags={"Domain"},
     *   summary="Update domain details",
     *   description="",
     *   operationId="add_or_update_domain",
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
    public function add_or_update_domain(Request $request) {
        $json_data = $request->getContent();
        $domain_data_array = json_decode($json_data, true);

        if ($domain_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }
        $domain_id = "";
        if (isset($domain_data_array['_id'])) {
            $domain_id = $domain_data_array['_id'];
        }
        $title = "";
        if (isset($domain_data_array['title'])) {
            $title = $domain_data_array['title'];
        }
        $code = "";
        if (isset($domain_data_array['code'])) {
            $code = $domain_data_array['code'];
        }
        

        $domain_array = array(
            '_id' => $domain_id,
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
                'code.required' => config('error_constants.code_required'),
                'title.required' => config('error_constants.domain_title_required'),
            ];
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required',
                'title' => 'required',
                'code' => 'required',
            );
            $messages = [
                '_id.required' => config('error_constants.domain_id_invalid'),
                'code.required' => config('error_constants.code_required'),
                'title.required' => config('error_constants.domain_title_required'),
            ];
        }
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($domain_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $domainModel = new DomainModel();
        $domainModel->create_or_update_domain($domain_array, $domain_id);
        if ($domain_id != "") {
            $success_msg = 'Domain Updated Successfully';
        } else {
            $success_msg = 'Domain Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/domain",
     *   tags={"Domain"},
     *   summary="delete domain data",
     *   description="Delete domain from system",
     *   operationId="delete_domain",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the domain that needs to be deleted",
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
    function delete_domain(Request $request) {
        $domain_id = trim($request->_id);
        $domainModel = new DomainModel();
        $domain_data = $domainModel->find_domain_details($domain_id);
        if ($domain_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.domain_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.domain_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        DomainModel::destroy($domain_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
