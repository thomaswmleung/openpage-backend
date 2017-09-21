<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\Helpers\ErrorMessageHelper;
use App\KeywordModel;
use App\Helpers\KeywordHelper;

class KeywordController extends Controller {
    /**
     * @SWG\Get(path="/keyword",
     *   tags={"Keyword"},
     *   summary="Returns list of keywords",
     *   description="Returns keywords data",
     *   operationId="keyword_list",
     *   produces={"application/json"},
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
     * @SWG\Get(path="/keyword/{_id}",
     *   tags={"Keyword"},
     *   summary="Returns keyword data",
     *   description="Returns keyword data",
     *   operationId="keyword_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the keyword that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid keyword id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function keyword_list(Request $request) {
        $keywordModel = new KeywordModel();

        if (isset($request->_id) && $request->_id != "") {

            $keyword_id = $request->_id;
            $keyword_details = $keywordModel->find_keyword_details($keyword_id);
            if ($keyword_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.keyword_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.keyword_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $keyword_details, "errors" => array());
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

            $keyword_details = $keywordModel->keyword_details($query_details);
            $total_count = $keywordModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $keyword_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/keyword",
     *   tags={"Keyword"},
     *   summary="Create a keyword",
     *   description="Create a keyword",
     *   operationId="create_or_update_keyword",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Keyword json input",
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
     * @SWG\Put(path="/keyword",
     *   tags={"Keyword"},
     *   summary="Update keyword details",
     *   description="",
     *   operationId="create_or_update_keyword",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="keyword json input",
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
    public function create_or_update_keyword(Request $request) {

        $json_data = $request->getContent();
        $keyword_data_array = json_decode($json_data, true);
        if ($keyword_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }
        $keyword_id = "";
        if (isset($keyword_data_array['_id'])) {
            $keyword_id = $keyword_data_array['_id'];
        }
        $keyword = "";
        if (isset($keyword_data_array['keyword'])) {
            $keyword = $keyword_data_array['keyword'];
        }
       
        

        $keyword_array = array(
            'keyword' => $keyword
        );

        $rules = array(
            'keyword' => 'required'
        );
        if($keyword_id!=""){
            $rules['_id'] = 'exists:keyword,_id';
            $keyword_array['_id'] = $keyword_data_array['_id'];
        }
        
        $messages = [
            'keyword.required' => config('error_constants.keyword_required'),            
            '_id.exists' => config('error_constants.keyword_id_invalid'),            
        ];
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($keyword_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
       
            $document_id = "";
            if (isset($keyword_data_array['document_id'])) {
                $document_id = $keyword_data_array['document_id'];
            }
            
            $type = "";
            if (isset($keyword_data_array['type'])) {
                $type = $keyword_data_array['type'];
            }

            $keywordHelper = new KeywordHelper;
            $key = $keywordHelper->add_or_update_keyword($keyword, $document_id, $type, $keyword_id);

            if ($key == $keyword_id) {
                $success_msg = 'Keyword Updated Successfully';
            } else {
                $success_msg = 'Keyword Added Successfully';
            }

            $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }
    
    public function getKeywordId(Request $request) {
       KeywordHelper::indexKeyword($request->keyword,"","");
    }
    
    /**
     * @SWG\Delete(path="/keyword",
     *   tags={"Keyword"},
     *   summary="delete keyword data",
     *   description="Delete keyword from system",
     *   operationId="delete_keyword",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the keyword that needs to be deleted",
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
    function delete_keyword(Request $request) {
        $keyword_id = trim($request->_id);
        $keywordModel = new KeywordModel();
        $keyword_data = $keywordModel->find_keyword_details($keyword_id);
        if ($keyword_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.keyword_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.keyword_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        KeywordModel::destroy($keyword_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }
}
