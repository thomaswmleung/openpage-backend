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
        $return_value = FALSE;
        $keyword_id = KeywordModel::getKeywordId($keyword);

        if ($keyword_id == NULL) {
            $keyword_id = KeywordHelper::create_keyword($keyword);
        }
        if ($document_id != "" && $document_type != "") {

            $insert_data = array(
                'document_id' => $document_id,
                'keyword_id' => $keyword_id,
                'type' => $document_type
            );

            $keywordIndexModel = new KeywordIndexModel();
            $keywordIndexModel->create_or_update_keyword_index($insert_data, $keyword_id);
            $return_value = TRUE;
        }
        return $return_value;
    }

    public static function create_keyword($keyword) {
        $insert_data = array(
            'keyword' => $keyword
        );
        $keyword_data = KeywordModel::create($insert_data);
        return $keyword_data->id;
    }

}
