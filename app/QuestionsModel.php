<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class QuestionsModel extends Eloquent {

    protected $collection = 'questions';
    protected $fillable = array('question_no', 'answer_cols', 'question_text', 'image', 'answer', 'question_type', 'x', 'y');

    public function add_questions($insert_data, $question_id) {

        $result = QuestionsModel::updateOrCreate(
                        ['_id' => $question_id], $insert_data
        );
        return $result;
    }

    public function question_list($data_array = NULL, $search_key = NULL, $skip = NULL, $limit = NULL) {
        if ($data_array == NULL) {
            $question_data = QuestionsModel::
//                    where('question_text', 'like', '%'.$search_key.'%')
                    skip($skip)->take($limit)->get();
        } else {
            $question_data = QuestionsModel::where($data_array)->get();
        }
        return $question_data;
    }

    public function fetch_all_questions() {
        $question_data = QuestionsModel::get();
        return $question_data;
    }

    public static function get_question_details($question_id) {
        $question_details = QuestionsModel::find($question_id);
        return $question_details;
    }

    public function question_details($query_details = NULL) {
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
            $question_data = QuestionsModel::where('question_text', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $question_data = QuestionsModel::skip($skip)->take($limit)->get();
        }
        return $question_data;
    }

    public function question_search($page_data) {
        $search_key = $page_data['search_key'];
        $skip = $page_data['skip'];
        $limit = $page_data['limit'];
        $question_query = QuestionsModel::where('question_text', 'like', '%' . $search_key . '%');
        $question_query->skip($skip);
        $question_query->take($limit);
        return $question_query->get();
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = QuestionsModel::where('question_text', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = QuestionsModel::count();
        }
        return $total_count;
    }

    public function find_question_details($question_id) {
        $question_data = QuestionsModel::find($question_id);
        return $question_data;
    }

}
