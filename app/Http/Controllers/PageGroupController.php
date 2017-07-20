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
use App\OverlayModel;
use App\BackgroundModel;
use App\PageModel;
use App\PageGroupModel;
use App\Helpers\Pdf_helper;

/*
 *  Class Name : PageGroupController
 *  Description : This controller handles parsing of JSON DATA and save to DB
 * 
 * 
 */

class PageGroupController extends Controller {

    public function create_page_group() {

        /* Temporly reading from file */
        $json_data = file_get_contents(url('pdf_page.json'));
        $page_data_array = json_decode($json_data, true);

        $page_group_model = new PageGroupModel();
        $page_group_id = $page_group_model->create_page_group();

        $page_data_array['_id'] = $page_group_id;
        $req_json = json_encode($page_data_array);
        $pdf_helper = new Pdf_helper();
        
        $response_array = array();
        $response_array['page_group_id'] = $page_group_id;
        if (sizeof($page_data_array['page_group']['page']) > 0) {
            $pdf_response_json = $pdf_helper->generate_pdf_from_json($req_json);

            $page_data_array = json_decode($pdf_response_json, true);

            if (isset($page_data_array['page_group'])) {

                $page_array = $page_data_array['page_group']['page'];


                $page_ids = array();

                foreach ($page_array as $page) {

                    $main = $page['main'];



                    $section_ids = array();
                    $page_section_array = $main['section'];

                    foreach ($page_section_array as $section) {

                        $questions_ids = array();
                        $section_question_array = $section['question'];

                        foreach ($section_question_array as $question) {

                            $question_number = $question['question_no'];
                            $question_text = $question['question_text'];

                            $question_type = $question['question_type'];
                            $answer_col = $question['answer_cols'];

                            $question_image_url = $question['image'];
                            $answer_array = $question['answer'];



                            $insert_data = array(
                                'question_no' => $question_number,
                                'answer_cols' => $answer_col,
                                'question_text' => $question_text,
                                'image' => $question_image_url,
                                'answer' => $answer_array,
                                'question_type' => $question_type,
                                'x' => $question['x'],
                                'y' => $question['y']
                            );


                            $question_id = $this->create_question($insert_data);
                            ;

                            array_push($questions_ids, $question_id);
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

                        $section_id = $this->create_section($insert_data);


                        array_push($section_ids, $section_id);
                    }

                    $main_header_text = $main['header_text'];
                    $main_footer_text = $main['footer_text'];

                    $insert_main_data = array(
                        'header_text' => $main_header_text,
                        'footer_text' => $main_footer_text,
                        'section' => $section_ids
                    );

                    $main_id = $this->create_main($insert_main_data);



                    $ovelay_data = $page['overlay'];
                    $page_ovelay_id = $this->create_overlay($ovelay_data);


                    $back_ground_data = $page['background'];


                    $page_back_ground_id = $this->create_background($back_ground_data);
                    $page_remark = $page['remark'];



                    $insert_page_data = array(
                        'ovelay' => $page_ovelay_id,
                        'main' => $main_id,
                        'background' => $page_back_ground_id,
                        'remark' => $page_remark
                    );

                    $page_id = $this->create_page($insert_page_data);

                    array_push($page_ids, $page_id);
                }

                $page_group_insert_data = array(
                    'page' => $page_ids
                );



                $pageGroup_result = $page_group_model->update_page_group($page_group_insert_data, $page_group_id);

               

                //echo '<pre>';var_dump($page_ids);
            } else {
// page is not defined.
            }
            
            
            $response_array['preview_url'] = $page_data_array['preview_url'];
            return json_encode($response_array);
        }
    }

    function create_question($insert_data) {
        $questionModel = new QuestionsModel();
        $questionDetails = $questionModel->add_questions($insert_data);
        return $questionDetails->_id;
    }

    function create_section($insert_data) {
        $sectionModel = new SectionModel();
        $sectionDetails = $sectionModel->add_section($insert_data);
        return $sectionDetails->_id;
    }

    function create_main($insert_data) {
        $mainModel = new MainModel();
        $model_details = $mainModel->add_main($insert_main_data);
        return $model_details->_id;
    }

    function create_overlay($insert_data) {
        $overlayModel = new OverlayModel();
        $overlay_result = $overlayModel->add_overlay(array('overlay' => $ovelay_data));
        return $overlay_result->_id;
    }

    function create_background($insert_data) {
        $backgroundModel = new BackgroundModel();
        $back_ground_result = $backgroundModel->add_back_ground(array('background' => $insert_data));
        return $back_ground_result->_id;
    }

    function create_page($insert_page_data) {
        $pageModel = new PageModel();
        $page_result = $pageModel->add_page($insert_page_data);
        return $page_result->_id;
    }

}
