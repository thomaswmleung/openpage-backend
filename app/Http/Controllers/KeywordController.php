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
    
    public function create_or_update_keyword(Request $request) {

         
        $keyword = $request->keyword;
        

        $keyword_array = array(
            'keyword' => $keyword
        );

        $rules = array(
            'keyword' => 'required'
        );
        
        $messages = [
            'keyword.required' => config('error_constants.keyword_required'),            
        ];
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($keyword_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            $keyword_id = "";
            if (isset($request->keyword_id)) {
                $keyword_id = $request->keyword_id;
            }
            
            $document_id = "";
            if (isset($request->document_id)) {
                $document_id = $request->document_id;
            }
            
            $type = "";
            if (isset($request->type)) {
                $type = $request->type;
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
}
