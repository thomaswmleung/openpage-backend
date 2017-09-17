<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\Helpers\ErrorMessageHelper;
use App\KeywordModel;
use App\Helpers\KeywordHelper;

class KeywordController extends Controller {

    public function create_or_update_keyword(Request $request) {

        $keyword_id = ""; // 
        $keyword = $request->keyword;
        $document_id = "";
        $type = "";

        $keyword_array = array(
            'keyword' => $keyword
        );

        $rules = array(
            'keyword' => 'required'
        );
        //dd($keyword_array);
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

            if (isset($_REQUEST['keyword_id'])) {
                $keyword_id = $_REQUEST['keyword_id'];
            }

            if (isset($_REQUEST['document_id'])) {
                $document_id = $_REQUEST['document_id'];
            }

            if (isset($_REQUEST['type'])) {
                $type = $_REQUEST['type'];
            }

            $keywordHelper = new \App\Helpers\KeywordHelper;
            $key = $keywordHelper->create_or_update_keyword($keyword, $document_id, $type, $keyword_id);

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
