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

}
