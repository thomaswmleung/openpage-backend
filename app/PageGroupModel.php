<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;

class PageGroupModel extends Eloquent {

    protected $collection = 'page_group';
    protected $fillable = array('page', 'preview_url');

    public function add_page_group($insert_data) {
        $result = PageGroupModel::create($insert_data);
        return $result;
    }

    public function create_page_group() {
        $result = PageGroupModel::create();
        return $result->_id;
    }

    public function update_page_group($update_data, $page_group_id) {
        $result = PageGroupModel::find($page_group_id)->update($update_data);
    }

    public function get_details($page_group_id) {

        
        $page_group_model = PageGroupModel::find([$page_group_id])->first();
        $page_group_info['_id'] = $page_group_id;
        if(isset($page_group_model->page) && $page_group_model->page != NULL ){
            $page_id_array = $page_group_model->page;
            
            $page_info_array = array();
            for($i=0; $i < sizeof($page_id_array); $i++) {
                $page_info = array();
                $page_id = $page_id_array[$i];
                $page_model = PageModel::find($page_id);
                $page_info['_id'] = $page_model->_id;
                $page_info['overlay'] = $page_model->overlay;
               
                $page_info['background'] = $page_model->background;
                $page_info['remark'] = $page_model->remark;
//                dd($page_model);
                $main_id = $page_model->main_id;
                $main_model = MainModel::find($main_id);
                
                $main_info_array = array();
                $main_info_array['_id'] = $main_model->_id;
                $main_info_array['header_text'] = $main_model->header_text;
                $main_info_array['footer_text'] = $main_model->footer_text;
               
                $section_id_array = $main_model->section;
                
                $section_details_array = array();
                for($section_index=0;$section_index < sizeof($section_id_array);$section_index++){
                    
                    $sectionid = $section_id_array[$section_index];
                    $section_details_array['_id'] = $sectionid;
                    $section_model = SectionModel::find($sectionid);
                    
                    $section_details_array['instruction_text'] = $section_model->instruction_text;
                    $section_details_array['section_type'] = $section_model->section_type;
                    $section_details_array['start_question_no'] = $section_model->start_question_no;
                    $section_details_array['with_sample_question'] = $section_model->with_sample_question;
                    $section_details_array['answer_cols'] = $section_model->answer_cols;
                    $section_details_array['suggestion_box'] = $section_model->suggestion_box;
                    
                    $question_id_array = $section_model->question;
                    $questions_array = array();
                    foreach ($question_id_array as $question_id){
                        $question_model = QuestionsModel::find($question_id);
                        $question_info_array['_id'] = $question_model->_id;
                        $question_info_array['question_no'] = $question_model->question_no;
                        $question_info_array['answer_cols'] = $question_model->answer_cols;
                        $question_info_array['question_text'] = $question_model->question_text;
                        $question_info_array['image'] = $question_model->image;
                        $question_info_array['x'] = $question_model->x;
                        $question_info_array['y'] = $question_model->y;
                        $question_info_array['answers'] = $question_model->answer;
                        
                        $questions_array[] = $question_info_array;
                        
                    }
                    $section_details_array['question'] = $questions_array;
 
                }
                
                $main_info_array['section'] = $section_details_array;
               
                $page_info['main'] = $main_info_array;
                
               $page_info_array[$i] = $page_info;
            }
            $page_group_info['page']= $page_info_array;
           

        }
        
        return $page_group_info;
    }

}
