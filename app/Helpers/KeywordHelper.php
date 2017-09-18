<?php

namespace App\Helpers;

use App\KeywordModel;
use App\KeywordIndexModel;
use Illuminate\Support\Facades\Validator;

class KeywordHelper {

    public function add_or_update_keyword($keyword, $document_id, $type, $keyword_id) {

        $insert_data = array(
            'keyword' => $keyword
        );

        $keywordModel = new KeywordModel();
        $latest_keyword_id = $keywordModel->add_or_edit_keyword($insert_data, $keyword_id);

        if ($document_id != "" && $type != "") {

            $insert_data = array(
                'document_id' => $document_id,
                'keyword_id' => $latest_keyword_id,
                'type' => $type
            );

            $keywordIndexModel = new KeywordIndexModel();
            $keywordIndexModel->create_or_update_keyword_index($insert_data, $latest_keyword_id);
        }

        return $latest_keyword_id;
    }

    public static function indexKeyword($keyword, $document_id, $document_type) {

        // validating $keyword_id
//        $rules = array(
//            'keyword_id' => 'exists:keyword,_id'
//        );
//        $validator = Validator::make(array('keyword_id' => $keyword_id), $rules);
        
        $keyword_model = new KeywordModel();
        $keyword_id = $keyword_model->getKeywordId($keyword);
        var_dump($keyword_id);
        exit();
                
        
        if (!$validator->fails()) {
            if ($document_id != "" && $type != "") {

                $insert_data = array(
                    'document_id' => $document_id,
                    'keyword_id' => $keyword_id,
                    'type' => $document_type
                );

                $keywordIndexModel = new KeywordIndexModel();
                $keywordIndexModel->create_or_update_keyword_index($insert_data, $keyword_id);
            }
        }
    }

}
