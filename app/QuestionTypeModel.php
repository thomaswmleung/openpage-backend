<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class QuestionTypeModel extends Eloquent {

    protected $collection = 'question_type';
    protected $fillable = array('type', 'block');

    public function question_type_details($query_details = NULL) {
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
            $question_type_data = QuestionTypeModel::where('type', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $question_type_data = QuestionTypeModel::skip($skip)->take($limit)->get();
        }
        return $question_type_data;
    }

    public function update_question_type($data) {
        $result = QuestionTypeModel::find($data['_id'])->update($data);
        return $result;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = QuestionTypeModel::where('type', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = QuestionTypeModel::count();
        }
        return $total_count;
    }

    public function find_question_type($question_type_id) {
        $question_type_data = QuestionTypeModel::find($question_type_id);
        return $question_type_data;
    }

}
