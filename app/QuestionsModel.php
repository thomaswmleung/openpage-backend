<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class QuestionsModel extends Eloquent {

    protected $collection = 'questions';
    protected $fillable = array('question_no', 'answer_cols', 'question_text', 'image', 'answer', 'question_type', 'x', 'y');

    public function add_questions($insert_data, $question_id) {
        
       // dd($insert_data);

//        $result = QuestionsModel::create($insert_data);
        $result = QuestionsModel::updateOrCreate(
                        ['_id' => $question_id], 
                        $insert_data
        );
        return $result;
    }

}
