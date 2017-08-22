<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\QuestionsModel;
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

    public static function get_section_details($section_id) {
        $section_details = SectionModel::find($section_id);
        
        $question_id_array = $section_details->question;
        $question_details_array = array();
        foreach($question_id_array as $question_id){
            $q_details = QuestionsModel::get_question_details($question_id);
            array_push($question_details_array, $q_details);
        }
        $section_details->question = $question_details_array;
        return $section_details;
    }
    
   public function section_list($section_array = NULL) {
        if ($section_array != NULL) {
            $section_data = SectionModel::where($section_array)->first();
        } else {
            $section_data = SectionModel::all();
        }
        return $section_data;
    }
    
    public function section_search($page_data) {
        $search_key = $page_data['search_key'];
        $skip = $page_data['skip'];
        $limit = $page_data['limit'];
        $section_query = SectionModel::where('instruction_text', 'like', '%' . $search_key . '%');
        $section_query->orwhere('section_type', 'like', '%' . $search_key . '%');
        $section_query->skip($skip);
        $section_query->take($limit);
        return $section_query->get();
    }
}
