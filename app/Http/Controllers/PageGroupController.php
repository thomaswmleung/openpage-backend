<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Curl_helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Helpers\tFPDF;
use App\QuestionsModel;
use App\SectionModel;
use App\MainModel;

/*
 *  Class Name : PageGroupController
 *  Description : This controller handles parsing of JSON DATA
 * 
 * 
 */

class PageGroupController extends Controller {

    public function create_page_group() {

        /* Temporly reading from file */
        $json_data = file_get_contents(url('pdf_page.json'));
        $page_data_array = json_decode($json_data, true);



        if (isset($page_data_array['page_group']['page'])) {

            $page_array = $page_data_array['page_group']['page'];

            foreach ($page_array as $page) {

                $background_data = $page['background'];
                $main_data_array = $page['main'];

                //$page_header_text = $page['main']['header_text'];
                
                $main_ids = array();

                foreach ($main_data_array as $main) {                    
                
                    $section_ids = array();
                    $page_section_array = $main['section'];

                    foreach ($page_section_array as $section) {

                        $questions_ids = array();
                        $section_question_array = $section['question'];

                        foreach ($section_question_array as $questions) {

                            $question_number = $questions['question_no'];
                            $question_text = $questions['question_text'];

                            $question_type = $questions['question_type'];
                            $answer_col = $questions['answer_cols'];

                            $question_image_url = $questions['image'];
                            $answer_array = $questions['answer'];



                            $insert_data = array(
                                'question_no' => $question_number,
                                'answer_cols' => $answer_col,
                                'question_text' => $question_text,
                                'image' => $question_image_url,
                                'answer' => $answer_array,
                                'question_type' => $question_type
                            );

                            $questionModel = new QuestionsModel();
                            $result_id = $questionModel->add_questions($insert_data);

                            array_push($questions_ids, $result_id['_id']);
                        }

                      

                        $section_instruction_text = $section['instruction_text'];
                        $section_type = $section['section_type'];
                        $section_start_question_no = $section['start_question_no'];
                        $section_with_sample_question = $section['with_sample_question'];
                        $section_answer_cols = $section['answer_cols'];
                        $section_suggestion_box = $section['suggestion_box'];                        
                        $section_question = $questions_ids;

                        $insert_data = array(
                            'instruction_text' => $section_instruction_text,
                            'section_type' => $section_type,
                            'start_question_no' => $section_start_question_no,
                            'with_sample_question' => $section_with_sample_question,
                            'answer_cols' => $section_answer_cols,
                            'suggestion_box' => $section_suggestion_box,
                            'question' => $section_question
                        );


                        $sectionModel = new SectionModel();
                        $result_id = $sectionModel->add_section($insert_data);

                        array_push($section_ids, $result_id['_id']);


                    }

                    $main_header_text = $main['header_text'];
                    $main_footer_text = $main['footer_text'];

                    $insert_main_data = array(
                        'header_text' => $main_header_text,
                        'footer_text' => $main_footer_text,
                        'section' => $section_ids
                    );

                    $mainModel = new MainModel();
                    $result_id = $mainModel->add_main($insert_main_data);

                    array_push($main_ids, $result_id['_id']);
                }
            }
        } else {
// page is not defined.
        }
    }

}
