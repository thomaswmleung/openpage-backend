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

/*
 *  Class Name : PdfController
 *  Description : This controller handles pdf generation
 * 
 * 
 */

class PdfController extends Controller {

    // This function helps to generate PDF
    public function generate_pdf() {
        $json_data = file_get_contents(url('pdf_page.json'));
        $page_data_array = json_decode($json_data, true);
        $fpdf = new Fpdf();

        $fpdf->SetFont('Courier', '', 15);
//        $test = iconv('UCS-4','UTF-8',"你好 世界");
//        var_dump($test);
//        exit();
//        $fpdf->Cell(40,10,'Hello World! -->'.$test);
//        $fpdf->Output();
//        exit;

        if (isset($page_data_array['page_group']['page'])) {

            $page_array = $page_data_array['page_group']['page'];

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
                foreach ($page_section_array as $section) {
                    $section_instruction_text = $section['instruction_text'];
                    // display section instruction
                    $fpdf->SetXY(10, $fpdf->GetY() + 5);
                    $fpdf->MultiCell(200, 5, $section_instruction_text, 0, 'C');
                    $section_question_array = $section['question'];
                    $currentY = $fpdf->GetY() + 5;
                    foreach ($section_question_array as $questions) {

                        $fpdf->SetXY(10, $currentY);
                        $question_number = $questions['question_no'];
                        $question_text = $questions['question_text'];
//                        $question_type = $questions['question_type'];
                        $question_image_url = $questions['image'];
                       
                        $fpdf->MultiCell(200, 5, $question_text, 0, 'L');
                        $currentY = $fpdf->GetY()+5;
                        
                        $imgAttrArray = getimagesize($question_image_url);
                        
                        $imgRatio = $imgAttrArray[0]/$imgAttrArray[1];
                        
                        
                         if (isset($question_image_url) AND $question_image_url != "" AND $question_image_url != NULL) {
                            $fpdf->Image($question_image_url,10,$currentY , 100,20);
                        }
                        $currentY += 20; 
                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(200, 5, "Answers: ", 0, 'L');
                        $currentY = $fpdf->GetY() + 3;

                        $answer_array = $questions['answer'];
                        foreach ($answer_array as $answer) {
                            $fpdf->SetXY(10, $currentY);

                            $answer_text = $answer['text'];
//                            $imge_url = $answer['url'];
//                            $is_correct_answer = $answer['answer'];

                            $fpdf->MultiCell(200, 5, $answer_text, 0, 'L');
                            $currentY = $fpdf->GetY();
                            $fpdf->SetXY(10, $currentY);
                        }

                        $currentY = $fpdf->GetY() + 5;
                    }


                    //Overlay Data

                    $overlay_data = $page['ovelay'];
                    // fectching only image part of overlay array

                    foreach ($overlay_data as $overlay) {
                        $ovelay_type = $overlay['type'];
                        if ($ovelay_type == "image") {
                            $image_path = $overlay['url'];
                            $image_x = $overlay['x'];
                            $image_y = $overlay['y'];
                            $image_w = $overlay['w'];
                            $image_h = $overlay['h'];

                            // display of overlay image
                            $fpdf->Image($image_path, $image_x, $image_y, $image_w, $image_h);
                        }
                        if ($ovelay_type == "text") {
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
                }
//                $fpdf->AddPage();
            }
        } else {
            // page is not defined.
        }

        $fpdf->Output('test.pdf', 'I');
        exit();
    }

    function Header($header_data) {
        $this->SetFont('Courier', 'B', 15);
        $this->Image('logo.png', 5, 5, 202.5, 47);
        $this->Image('watermark.png', 30, 90, 150, 110);
    }

    function footer($footer_data) {
        $this->SetFont('Courier', 'B', 15);
        $this->Image('logo.png', 5, 5, 202.5, 47);
        $this->Image('watermark.png', 30, 90, 150, 110);
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            return true;
    }

}
