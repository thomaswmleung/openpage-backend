<?php

namespace App\Helpers;

use App\Helpers\tFPDF;
use App\Helpers\GCS_helper;
use Illuminate\Support\Facades\Config;

class Pdf_helper {

    public function generate_pdf_from_json($json_data) {

        $page_data_array = json_decode($json_data, true);
        $isValidJson = TRUE;

        $fpdf = new tFPDF();
        $fpdf->AddFont('msjh', '', 'msjh.ttf', true);
        $fpdf->SetFont('msjh', '', 14);
        $responseArray = $page_data_array;

        if (isset($page_data_array['page_group']['page'])) {
            $responseArray['page_group']['page'] = array();
            $page_array = $page_data_array['page_group']['page'];
            $responseArray['page_group']['page'] = $page_array;
            $pageCOunt = 0;



            foreach ($page_array as $page) {


                // create new blank page
                $fpdf->AddPage();

                // displaying background image
                $background_data = $page['background'];
                $background_image_path = $background_data[0]['url'];

                // display image only if image exist.
                if (isset($background_image_path) && $background_image_path != "" && $background_image_path != NULL) {
                    $bg_image_x = $background_data[0]['x'];
                    $bg_image_y = $background_data[0]['y'];
                    $bg_image_w = $background_data[0]['w'];
                    $bg_image_h = $background_data[0]['h'];

                    $fpdf->Image($background_image_path, $bg_image_x, $bg_image_y, $bg_image_w, $bg_image_h);
                }

                // display text only if text exist.
                if (isset($background_data[0]['string']) && $background_data[0]['string'] != "" && $background_data[0]['string'] != NULL) {
                    $background_text = $background_data[0]['string'];
                    $text_x = $background_data[0]['x'];
                    $text_y = $background_data[0]['y'];
                    $text_w = $background_data[0]['w'];
                    $text_h = $background_data[0]['h'];
                    // Set font size based on w and h 
                    $fpdf->MultiCell($text_x, $text_y, $background_text, 0, 'C');
                }



                $main_data_array = $page['main'];
                $page_header_text = $page['main']['header_text'];
                // define pdf header here
                $fpdf->MultiCell(200, 10, $page_header_text, 0, 'C');

                $page_footer_text = $page['main']['footer_text'];
                // define pdf footer here

                $page_section_array = $page['main']['section'];
                $sectionCount = 0;


                foreach ($page_section_array as $section) {

                    $section_instruction_text = $section['instruction_text'];
                    // display section instruction
                    $fpdf->SetXY(10, $fpdf->GetY() + 5);
                    $fpdf->MultiCell(200, 5, $section_instruction_text, 0, 'C');
                    $section_question_array = $section['question'];

//                    if (sizeof($section_question_array) == 0) {
//                        $isValidJson = FALSE;
//                    }

                    $currentY = $fpdf->GetY() + 5;

                    $questionsRespoonseArray = array();

                    foreach ($section_question_array as $question) {

                        $fpdf->SetXY(10, $currentY);
                        $question['x'] = 10;
                        $question['y'] = $currentY;
                        $question_number = $question['question_no'];
                        $question_text = $question['question_text'];
//                        $question_type = $questions['question_type'];
                        $question_image_url = $question['image'];

                        $fpdf->MultiCell(200, 5, $question_text, 0, 'L');
                        $currentY = $fpdf->GetY() + 5;

                        $imgAttrArray = getimagesize($question_image_url);

                        $imgRatio = $imgAttrArray[0] / $imgAttrArray[1];


                        if (isset($question_image_url) AND $question_image_url != "" AND $question_image_url != NULL) {
                            $fpdf->Image($question_image_url, 10, $currentY, 100, 20);
                        }
                        $currentY += 20;
                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(200, 5, "Answers: ", 0, 'L');
                        $currentY = $fpdf->GetY() + 3;

                        $answer_array = $question['answer'];
//                        if (sizeof($answer_array) == 0) {
//                            $isValidJson = FALSE;
//                        }

                        $responseAnswersArray = array();
                        foreach ($answer_array as $answer) {
                            $fpdf->SetXY(10, $currentY);
                            $answer['x'] = 10;
                            $answer['y'] = $currentY;
                            $responseAnswersArray[] = $answer;
                            $answer_text = $answer['text'];
//                            $imge_url = $answer['url'];
//                            $is_correct_answer = $answer['answer'];

                            $fpdf->MultiCell(200, 5, $answer_text, 0, 'L');
                            $currentY = $fpdf->GetY();
                            $fpdf->SetXY(10, $currentY);
                        }
                        $question['answer'] = $responseAnswersArray;
                        $currentY = $fpdf->GetY() + 5;


                        $questionsRespoonseArray[] = $question;
                    }
                    $responseArray['page_group']['page'][$pageCOunt]['main']['section'][$sectionCount]['question'] = $questionsRespoonseArray;
                    $sectionCount++;
                }

                //Overlay Data

                $overlay_data = $page['overlay'];
                // fectching only image part of overlay array

                foreach ($overlay_data as $overlay) {
                    $overlay_type = $overlay['type'];
                    if ($overlay_type == "image") {
                        $image_path = $overlay['url'];
                        $image_x = $overlay['x'];
                        $image_y = $overlay['y'];
                        $image_w = $overlay['w'];
                        $image_h = $overlay['h'];

                        // display of overlay image
                        $fpdf->Image($image_path, $image_x, $image_y, $image_w, $image_h);
                    }
                    if ($overlay_type == "text") {
                        $overlay_text = $overlay['string'];
                        $text_x = $overlay['x'];
                        $text_y = $overlay['y'];
                        $text_w = $overlay['w'];
                        $text_h = $overlay['h'];

                        // Set font size based on w and h 
                        $fpdf->MultiCell($text_w, $text_h, $overlay_text, 0, 'C');
                    }

                    // TODO : for overlay type shape.
                }


                // display image only if image exist.
                if (isset($image_path) && $image_path != "" && $image_path != NULL) {
                    
                }

                // display text only if text exist.
                if (isset($overlay_data[0]['string']) && $overlay_data[0]['string'] != "" && $overlay_data[0]['string'] != NULL) {
                    
                }
                $pageCOunt++;
            }
        } else {
            // page is not defined.
            $isValidJson = FALSE;
        }

        if (!$isValidJson) {
            $responseArray['error'] = "INVALID JSON RECIEVED";
            return $responseArray;
        }

//        $pdf_name = uniqid() . ".pdf";
        $pdf_name = "test" . ".pdf";
        if (!file_exists(public_path('pdfs'))) {
            mkdir(public_path('pdfs'), 0777, true);
        }
        $pdf_path = public_path('pdfs' . DIRECTORY_SEPARATOR . $pdf_name);
        $fpdf->Output($pdf_path, 'F');

        // upload to GCS
//        $gcs_result = GCS_helper::upload_to_gcs('pdfs/' . $pdf_name);
//        if (!$gcs_result) {
//            $responseArray['error'] = "Error in upload of GCS";
//            return $responseArray;
//        }
//        // delete your local pdf file here
//        unlink($pdf_path);
//        
//        $pdf_url = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $pdf_name;
//        $responseArray['preview_url'] = $pdf_url;
        $responseArray['preview_url'] = "custom_url";

        return json_encode($responseArray);
    }

    static public function create_page($fpdf, $page_data) {

        // create new blank page
        $fpdf->AddPage();

        // displaying background image
        $background_data = $page['background'];
        $background_image_path = $background_data[0]['url'];

        // display image only if image exist.
        if (isset($background_image_path) && $background_image_path != "" && $background_image_path != NULL) {
            $bg_image_x = $background_data[0]['x'];
            $bg_image_y = $background_data[0]['y'];
            $bg_image_w = $background_data[0]['w'];
            $bg_image_h = $background_data[0]['h'];

            $fpdf->Image($background_image_path, $bg_image_x, $bg_image_y, $bg_image_w, $bg_image_h);
        }

        // display text only if text exist.
        if (isset($background_data[0]['string']) && $background_data[0]['string'] != "" && $background_data[0]['string'] != NULL) {
            $background_text = $background_data[0]['string'];
            $text_x = $background_data[0]['x'];
            $text_y = $background_data[0]['y'];
            $text_w = $background_data[0]['w'];
            $text_h = $background_data[0]['h'];
            // Set font size based on w and h 
            $fpdf->MultiCell($text_x, $text_y, $background_text, 0, 'C');
        }



        $main_data_array = $page['main'];
        $page_header_text = $page['main']['header_text'];
        // define pdf header here
        $fpdf->MultiCell(200, 10, $page_header_text, 0, 'C');

        $page_footer_text = $page['main']['footer_text'];
        // define pdf footer here

        $page_section_array = $page['main']['section'];
        $sectionCount = 0;


        foreach ($page_section_array as $section) {

            $section_instruction_text = $section['instruction_text'];
            // display section instruction
            $fpdf->SetXY(10, $fpdf->GetY() + 5);
            $fpdf->MultiCell(200, 5, $section_instruction_text, 0, 'C');
            $section_question_array = $section['question'];

//                    if (sizeof($section_question_array) == 0) {
//                        $isValidJson = FALSE;
//                    }

            $currentY = $fpdf->GetY() + 5;

            $questionsRespoonseArray = array();

            foreach ($section_question_array as $question) {

                $fpdf->SetXY(10, $currentY);
                $question['x'] = 10;
                $question['y'] = $currentY;
                $question_number = $question['question_no'];
                $question_text = $question['question_text'];
//                        $question_type = $questions['question_type'];
                $question_image_url = $question['image'];

                $fpdf->MultiCell(200, 5, $question_text, 0, 'L');
                $currentY = $fpdf->GetY() + 5;

                $imgAttrArray = getimagesize($question_image_url);

                $imgRatio = $imgAttrArray[0] / $imgAttrArray[1];


                if (isset($question_image_url) AND $question_image_url != "" AND $question_image_url != NULL) {
                    $fpdf->Image($question_image_url, 10, $currentY, 100, 20);
                }
                $currentY += 20;
                $fpdf->SetXY(10, $currentY);
                $fpdf->MultiCell(200, 5, "Answers: ", 0, 'L');
                $currentY = $fpdf->GetY() + 3;

                $answer_array = $question['answer'];
//                        if (sizeof($answer_array) == 0) {
//                            $isValidJson = FALSE;
//                        }

                $responseAnswersArray = array();
                foreach ($answer_array as $answer) {
                    $fpdf->SetXY(10, $currentY);
                    $answer['x'] = 10;
                    $answer['y'] = $currentY;
                    $responseAnswersArray[] = $answer;
                    $answer_text = $answer['text'];
//                            $imge_url = $answer['url'];
//                            $is_correct_answer = $answer['answer'];

                    $fpdf->MultiCell(200, 5, $answer_text, 0, 'L');
                    $currentY = $fpdf->GetY();
                    $fpdf->SetXY(10, $currentY);
                }
                $question['answer'] = $responseAnswersArray;
                $currentY = $fpdf->GetY() + 5;


                $questionsRespoonseArray[] = $question;
            }
            $responseArray['page_group']['page'][$pageCOunt]['main']['section'][$sectionCount]['question'] = $questionsRespoonseArray;
            $sectionCount++;
        }

        //Overlay Data

        $overlay_data = $page['overlay'];
        // fectching only image part of overlay array

        foreach ($overlay_data as $overlay) {
            $overlay_type = $overlay['type'];
            if ($overlay_type == "image") {
                $image_path = $overlay['url'];
                $image_x = $overlay['x'];
                $image_y = $overlay['y'];
                $image_w = $overlay['w'];
                $image_h = $overlay['h'];

                // display of overlay image
                $fpdf->Image($image_path, $image_x, $image_y, $image_w, $image_h);
            }
            if ($overlay_type == "text") {
                $overlay_text = $overlay['string'];
                $text_x = $overlay['x'];
                $text_y = $overlay['y'];
                $text_w = $overlay['w'];
                $text_h = $overlay['h'];

                // Set font size based on w and h 
                $fpdf->MultiCell($text_w, $text_h, $overlay_text, 0, 'C');
            }

            // TODO : for overlay type shape.
        }


        // display image only if image exist.
        if (isset($image_path) && $image_path != "" && $image_path != NULL) {
            
        }

        // display text only if text exist.
        if (isset($overlay_data[0]['string']) && $overlay_data[0]['string'] != "" && $overlay_data[0]['string'] != NULL) {
            
        }
        $pageCOunt++;

        return $fpdf;
    }

    static public function generate_book($input_json) {
        // temprorily reading it from file
        $json_data = file_get_contents(url('book.json'));
        $book_data_array = json_decode($json_data, true);

        $fpdf = new tFPDF();
//        $fpdf->AddFont('msjh', '', 'msjh.ttf', true);
        $fpdf->AddFont('msjhb', '', 'msjhb.ttf', true);
        $fpdf->AddFont('msjh', '', 'msjh.ttf', true);
        $fpdf->SetFont('msjhb', '', 18);

        $fpdf->AddPage();

        //setting book title

        $fpdf->SetXY(5, 5);
        $book_title = $book_data_array['cover']['title'];
        $fpdf->MultiCell(200, 10, $book_title, 0, 'C');
        $fpdf->SetXY(5, $fpdf->GetY());
        $sub_title = $book_data_array['cover']['subtitle'];
        $fpdf->SetFont('msjh', '', 12);
        $fpdf->MultiCell(200, 5, $sub_title, 0, 'C');

        $cover_img_url = $book_data_array['cover']['cover_image'];
        $fpdf->Image($cover_img_url, 5, $fpdf->GetY(), 200, 270);

        $school_logo = $book_data_array['cover']['school_logo'];
        $school_name = $book_data_array['cover']['school_name'];
        $fpdf->Image($school_logo, 25, 250, 30, 25);
        $fpdf->SetFont('msjh', '', 15);
        $fpdf->SetXY(55, 265);
        $fpdf->MultiCell(140, 5, $school_name, 0, 'L');


        //Table of contents
        $toc_array = $book_data_array['toc'];

        $fpdf->AddPage();
        $fpdf->SetXY(5, 55);
        $toc_col1x = 5;
        $toc_col2x = 50;
        $toc_col3x = 135;
        $toc_col4x = 200;

        $toc_col1_width = 45;
        $toc_col2_width = 85;
        $toc_col3_width = 65;
        $toc_col4_width = 20;

        $tocy = $fpdf->GetY();
        $fpdf->SetFont('msjh', '', 12);
        foreach ($toc_array as $toc) {

//            var_dump($toc);
//            exit();
            if ($toc['type'] == 'unit') {
                $fpdf->SetFillColor(147, 148, 150);
                $fpdf->MultiCell(205, 10, $toc['reference_text'], 0, 'L', true);
                $tocy = $fpdf->GetY();
            } else if ($toc['type'] == 'inline') {

                $fpdf->SetFont('msjh', '', 8);
                $fpdf->SetXY($toc_col1x, $tocy);
                $fpdf->MultiCell($toc_col1_width, 5, $toc['reference_text'], 0, 'L');
                $fpdf->SetFont('msjh', '', 12);
                $fpdf->SetXY($toc_col2x, $tocy + 0.5);
                $fpdf->SetFillColor(234, 238, 239);
                $fpdf->MultiCell($toc_col2_width, 10, $toc['language_knowledge'], 0, 'L', true);
//            
                $particular_str = "";
                foreach ($toc['particular'] as $particular) {
                    $particular_str .= $particular . "\n";
                }
                $fpdf->SetFont('msjh', '', 8);
                $fpdf->SetXY($toc_col3x, $tocy + 0.5);
                $fpdf->MultiCell($toc_col3_width, 5, $particular_str, 0, 'L');
                $fpdf->SetFont('msjh', '', 12);


                $fpdf->SetXY($toc_col4x, $tocy);
                $fpdf->MultiCell($toc_col4_width, 10, $toc['page_code'], 0, 'L');



                $tocy = $fpdf->GetY();

                $fpdf->Line(5, $tocy, 210, $tocy);

//            $fpdf->MultiCell(205, 10,$toc['reference_text'] , 0, 'L',true);
//            $fpdf->MultiCell(205, 10,$toc['reference_text'] , 0, 'L',true);
            }

            $fpdf->SetXY(5, $tocy);
        }

        $page_array = $book_data_array['page'];

        foreach ($page_array AS $page_details) {
            $fpdf->AddPage();
            $fpdf->SetXY(5, 5);
            $fpdf->SetFont('msjhb', '', 18);
            $fpdf->MultiCell(13, 10, $page_details['page_code'], 0);
            $fpdf->SetXY(18, 5);
            $fpdf->MultiCell(180, 10, $page_details['page_title'], 0);
            $fpdf->SetFont('msjh', '', 12);
            $fpdf->SetXY(5, 15);
            $fpdf->MultiCell(8, 5, $page_details['unit'], 0);
            $fpdf->SetXY(13, 15);
            $fpdf->MultiCell(8, 5, $page_details['topic'], 0);
            $fpdf->SetXY(5, 260);
            $fpdf->MultiCell(8, 5, $page_details['page_number'], 0);

            $author_info_array = $page_details['author'];
            $fpdf->SetFont('msjh', '', 8);
            $authory = 260;
            foreach ($author_info_array as $author_info) {
                $fpdf->SetXY(15, $authory);
                $fpdf->MultiCell(50, 4, $author_info, 0);
                $authory += 4;
            }
            $fpdf->SetXY(180, 260);
            $fpdf->MultiCell(50, 4, $page_details['royalty'], 0);


            $fpdf->SetFont('msjh', '', 12);
        }


        $fpdf->Output('sample_book.pdf', 'I');
    }

}
