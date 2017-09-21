<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Log;

class KeywordModel extends Eloquent {

    protected $collection = 'keyword';
    protected $fillable = array('keyword');

    public function add_or_edit_keyword($insert_data, $id) {
        $result = KeywordModel::updateOrCreate(
                        ['_id' => $id], $insert_data
        );
        return $result->_id;
    }

    public static function getKeywordId($keyword) {
        $keyword_data = KeywordModel::where(array('keyword' => $keyword))->first();
        if ($keyword_data == NULL || $keyword_data == "") {
            return NULL;
        }
        return $keyword_data->_id;
    }

    public function find_keyword_details($keyword_id) {
        $keyword_info = KeywordModel::find($keyword_id);
        return $keyword_info;
    }

    public function keyword_details($query_details = NULL) {

        if ($query_details == NULL) {
            $skip = 0;
            $limit = config('constants.default_query_limit');
            $search_key = "";
        } else {
            if (isset($query_details['skip'])) {
                $skip = $query_details['skip'];
            } else {
                $skip = 0;
            }
            if (isset($query_details['limit'])) {
                $limit = $query_details['limit'];
            } else {
                $limit = config('constants.default_query_limit');
            }
            if (isset($query_details['search_key'])) {
                $search_key = $query_details['search_key'];
            } else {
                $search_key = "";
            }
        }

        if ($search_key != "") {
            $keyword_data = KeywordModel::where('keyword', 'like', "%$search_key%")->skip($skip)->take($limit)->get();
        } else {
            $keyword_data = KeywordModel::skip($skip)->take($limit)->get();
        }
        return $keyword_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = KeywordModel::where('keyword', 'like', "%$search_key%")->count();
        } else {
            $total_count = KeywordModel::count();
        }
        return $total_count;
    }

}
