<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;

class QuestionsModel extends Eloquent {

    protected $collection = 'questions';
    protected $fillable = array('question_no','answer_cols','question_text','image','answer','question_type');
    
    
     public function add_questions($insert_data) {       
        $result = QuestionsModel::create($insert_data);
        return $result;
    }


    

}
