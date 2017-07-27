<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SectionModel extends Eloquent {

    protected $collection = 'section';
    protected $fillable = array('instruction_text','section_type','start_question_no','with_sample_question','answer_cols','suggestion_box','question');
    
    
     public function add_section($insert_data, $section_id) {       
       // $result = SectionModel::create($insert_data);
         $result = SectionModel::updateOrCreate(
                 ['_id' => $section_id],
                 $insert_data
                 );
        return $result;
    }


    

}
