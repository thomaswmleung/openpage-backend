<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class QuestionTypeModel extends Eloquent {

    protected $collection = 'question_type';
    protected $fillable = array('type', 'block');

    public function question_type_details($data_array = NULL) {
        if ($data_array != NULL) {
            $question_type_data = QuestionTypeModel::where($data_array)->first();
        } else {
            $question_type_data = QuestionTypeModel::all();
        }
        return $question_type_data;
    }

    public function update_question_type($data) {
        $result = QuestionTypeModel::find($data['_id'])->update($data);
        return $result;
    }

   

}
