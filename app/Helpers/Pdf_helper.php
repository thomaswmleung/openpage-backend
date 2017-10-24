<?php

namespace App\Helpers;

use App\Helpers\tFPDF;
use App\Helpers\GCS_helper;
use Illuminate\Support\Facades\Config;
use App\PageModel;
use Imagick;

class Pdf_helper {

    public function generate_pdf_from_json($json_data,$isTeacherCopy = FALSE) {

        $page_data_array = json_decode($json_data, true);
        $isValidJson = TRUE;

        $fpdf = new tFPDF();
        $fpdf->AddFont('msjh', '', 'msjh.ttf', true);
        $fpdf->SetFont('msjh', '', 14);
        $fpdf->SetAutoPageBreak(false);
        $responseArray = $page_data_array;

        if (isset($page_data_array['page_group']['page'])) {

            $pageCOunt = 0;

            if (isset($responseArray['page_group']['import_url']) &&
                    $responseArray['page_group']['import_url'] != null) {

                $filename = basename($responseArray['page_group']['import_url']);

                $uniqueId = uniqid();

                $pdf_path = public_path("tmp" . DIRECTORY_SEPARATOR . $uniqueId . $filename);
                GCS_helper::download_object($filename, $pdf_path);

                $im = new \Imagick($pdf_path);

                $count = $im->getNumberImages();

                for ($page_index = 0; $page_index < $count; $page_index++) {
                    $pdf_img = new Imagick();
                    $pdf_img->setresolution(210, 297);
                    $pdf_img->readimage($pdf_path . "[" . $page_index . "]");
//                    $pdf_img = new Imagick($pdf_path . "[" . $page_index . "]");
                    $pdf_img->setImageFormat('jpg');

                    $image_name_from_file = substr($filename, 0, strpos($filename, "."));
                    $image_name = $uniqueId . $image_name_from_file . $page_index . ".jpg";
                    $image_path = "tmp" . DIRECTORY_SEPARATOR . $image_name;
                    $image_absolute_path = public_path($image_path);


//                    $pdf_img->setResolution(2100, 2970);
//                    $pdf_img->setImageCompression(Imagick::COMPRESSION_JPEG);
//                    $pdf_img->setImageCompressionQuality(100);
                    $pdf_img->writeImage($image_absolute_path);

//                    var_dump($image_absolute_path);
//                    exit();
//                    

                    $gcs_result = GCS_helper::upload_to_gcs($image_path);

                    //upload image to GCS
                    if (!$gcs_result) {
                        $responseArray['error'] = "Error in upload of GCS";
                        return $responseArray;
                    }
                    unlink($image_path);
                    $pdf_img->destroy();
                    $gcs_path = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $image_name;
                    $background_data = array();
                    $background_data[0] = array(
                        'type' => "image",
                        'url' => $gcs_path,
                        'x' => 0,
                        'y' => 0,
                        'w' => 210,
                        'h' => 297);
                    $page_data = array(
                        'overlay' => array(),
                        'main_id' => "",
                        'background' => $background_data,
                        'remark' => "",
                        'is_imported' => TRUE
                    );

                    $page_model = new PageModel();
                    $page_model = $page_model->add_or_update_page($page_data);
                    $page_data['page_id'] = $page_model->_id;
                    array_push($page_data_array['page_group']['page'], $page_data);
                }
//                file_put_contents (public_path("tmp/test_1.jpg"), $im);
//                $im = new \Imagick(public_path("tmp".DIRECTORY_SEPARATOR.$filename));
//                var_dump(public_path("tmp".DIRECTORY_SEPARATOR."test_1.jpg"));


                unlink($pdf_path);
//                var_dump(sizeof($page_data_array['page_group']['page']));
//                exit();
            }

            $responseArray['page_group']['page'] = array();
            $page_array = $page_data_array['page_group']['page'];
            $responseArray['page_group']['page'] = $page_array;

            $actualPDFPageIndex = 0;
            foreach ($page_array as $page) {
                $actualPageIndexArray = array();
                array_push($actualPageIndexArray, $actualPDFPageIndex);
                // create new blank page
                $fpdf->AddPage();
                $actualPDFPageIndex++;
                // displaying background image
                $background_data = $page['background'];
                foreach ($background_data as $background) {
                    if ($background['type'] == "image") {
                        $background_image_path = $background['url'];
                        // display image only if image exist.
                        if (isset($background_image_path) && $background_image_path != "" && $background_image_path != NULL) {
                            $bg_image_x = $background['x'];
                            $bg_image_y = $background['y'];
                            $bg_image_w = $background['w'];
                            $bg_image_h = $background['h'];
                            $fpdf->Image($background_image_path, $bg_image_x, $bg_image_y, $bg_image_w, $bg_image_h);
                        }
                    }
                    if ($background['type'] == "text") {
                        // display text only if text exist.
                        if (isset($background['string']) && $background['string'] != "" && $background['string'] != NULL) {
                            $background_text = $background['string'];
                            $text_x = $background['x'];
                            $text_y = $background['y'];
                            $text_w = $background['w'];
                            $text_h = $background['h'];
                            // Set font size based on w and h 
                            $fpdf->MultiCell($text_x, $text_y, $background_text, 0, 'C');
                        }
                    }
                }

                if (isset($page['main'])) {

                    $fpdf->SetXY(10, 10);
                    $main_data_array = $page['main'];
                    $page_header_text = $page['main']['header_text'];
                    // define pdf header here
                    $fpdf->MultiCell(200, 10, $page_header_text, 0, 'C');

                    $page_footer_text = $page['main']['footer_text'];
                    // define pdf footer here

                    $page_section_array = $page['main']['section'];
                    $sectionCount = 0;


                    foreach ($page_section_array as $section) {

                        // Handling of different question types
                        if (isset($section['instruction']['text'])) {

                            $fpdf->SetXY(10, $fpdf->GetY());
                            $fpdf->MultiCell(200, 5, $section['instruction']['text'], 0, 'L');
                        }
//                        var_dump($section['paraBox']);
//                        exit();
                        if (isset($section['paraBox']['text'])) {
                            $fpdf->SetXY(10, $fpdf->GetY() + 5);
                            $fpdf->MultiCell(190, 10, str_replace('\r', "\n", $section['paraBox']['text']), 1, 'L');
                        }

                        if (isset($section['instruction_text'])) {
                            $section_instruction_text = $section['instruction_text'];
                            // display section instruction
                            $fpdf->SetXY(10, $fpdf->GetY() + 5);
                            $fpdf->MultiCell(200, 5, $section_instruction_text, 0, 'C');
                        }
                        $section_question_array = $section['question'];

//                    if (sizeof($section_question_array) == 0) {
//                        $isValidJson = FALSE;
//                    }

                        $currentY = $fpdf->GetY() + 5;

                        $questionsRespoonseArray = array();
                        $questionCount = 1;

//                        $isTeacherCopy = true;


                        foreach ($section_question_array as $question) {

                            $isQuestionCountDisplayed = FALSE;

                            if (isset($question['optBox']['option'])) {

                                $optionBoxString = implode("    ", $question['optBox']['option']);

                                $fpdf->SetXY(10, $currentY);
                                if (!$isQuestionCountDisplayed) {

                                    $fpdf->MultiCell(10, 7, $questionCount . " ) ", 0, 'L');
                                }
                                $fpdf->SetXY(20, $currentY);
                                $fpdf->MultiCell($fpdf->GetStringWidth($optionBoxString) + 5, 7, $optionBoxString, 1, 'L');
                                $isQuestionCountDisplayed = TRUE;

                                $currentY = $fpdf->GetY() + 5;
                            }

                            if (isset($question['text'])) {


                                if (!$isTeacherCopy) {
                                    $questionText = $question['text'];
                                    $blankIndexStart = strpos($questionText, "{{");
                                    $blankIndexEnd = strpos($questionText, "}}") - $blankIndexStart + 2;
                                    $questionText = substr_replace($questionText, "_________", $blankIndexStart, $blankIndexEnd);

                                    $questionY = $fpdf->GetY() + 5;
                                    $fpdf->SetXY(10, $questionY);


                                    if (!$isQuestionCountDisplayed) {

                                        $fpdf->MultiCell(10, 7, $questionCount . " ) ", 0, 'L');
                                    }
                                    $fpdf->SetXY(20, $questionY);
                                    $fpdf->MultiCell(200, 7, $questionText, 0, 'L');
                                    $currentY = $fpdf->GetY() + 5;
                                } else {




                                    $questionText = $question['text'];
//                                    $questionText = "This is the {{blank}} test ";
//                                    $questionText = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. "
//                                            . "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, "
//                                            . "when an unknown printer took a galley of type and scrambled it to make a type specimen book."
//                                            . " It has survived not only five centuries, but also the leap into electronic typesetting, "
//                                            . "remaining essentially unchanged. "
//                                            . "It was popularised in the 1960s with the release of Letraset sheets containing"
//                                            . " Lorem Ipsum passages, and more recently with {{desktop}} "
//                                            . "publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
                                    if (strpos($questionText, "{{") !== FALSE) {
                                        $blankIndexStart = strpos($questionText, "{{");
                                        $blankIndexEnd = strpos($questionText, "}}");

                                        $questionInitialString = substr($questionText, 0, $blankIndexStart);
                                        $questionLastString = substr($questionText, $blankIndexEnd + 2);

                                        $questionY = $fpdf->GetY() + 5;
                                        $fpdf->SetXY(10, $questionY);
//                                    $fpdf->MultiCell(170, 7,$questionText , 0, 'L');
//                                    $fpdf->Ellipse(100, 50, 30, 20,'F');
                                        if (!$isQuestionCountDisplayed) {
                                            $fpdf->MultiCell(10, 7, $questionCount . " ) ", 0, 'L');
                                        }
//                                    $fpdf->MultiCell(170, 7, $fpdf->GetStringWidth($questionText) . " ) ", 0, 'L');
                                        $fpdf->SetXY(20, $questionY);

//                                    $fpdf->newFlowingBlock( 170, 7, 0, 'L' );
//                                    $fpdf->MultiCell(170, 7, $questionInitialString, 0, 'L');

                                        $tempW = $fpdf->w;
                                        $tempLMargin = $fpdf->lMargin;
                                        $tempRMargin = $fpdf->rMargin;



                                        $fpdf->lMargin = $fpdf->x;
                                        $fpdf->rMargin = $fpdf->w - ($fpdf->x + 170);



                                        $fpdf->Write(7, $questionInitialString);

//                                        $fpdf->SetFont('msjh', 'U', 14);
                                        $answerString = substr($questionText, $blankIndexStart + 2, $blankIndexEnd - $blankIndexStart - 2);
//                                    $fpdf->MultiCell(170, 7,$answerString , 0, 'L');
//                                        $fpdf->SetFillColor(224,235,255);
//                                        $fpdf->Write(7, "__".$answerString."__");
                                        $answerString = "  " . $answerString . "  ";
                                        $fpdf->MultiCell($fpdf->GetStringWidth($answerString) + 5, 7, $answerString, 'B', 'L');
                                        $fpdf->SetFont('msjh', '', 14);
                                        $fpdf->Write(7, $questionLastString);

//                                    $this->w = $tempW;
                                        $fpdf->lMargin = $tempLMargin;
                                        $fpdf->rMargin = $tempRMargin;
                                    } else {
                                        $fpdf->SetXY(10, $currentY);
//                                    $fpdf->MultiCell(170, 7,$questionText , 0, 'L');
//                                    $fpdf->Ellipse(100, 50, 30, 20,'F');
                                        $fpdf->MultiCell(10, 7, $questionCount . " ) ", 0, 'L');
//                                    $fpdf->MultiCell(170, 7, $fpdf->GetStringWidth($questionText) . " ) ", 0, 'L');
                                        $fpdf->SetXY(20, $currentY);
                                        $fpdf->MultiCell(170, 7, $questionText, 0, 'L');
                                    }
//                                    $fpdf->finishFlowingBlock();
//                                    $initialStringLength = $fpdf->GetStringWidth($questionInitialString);
//                                    
//                                    $fpdf->SetXY($fpdf->GetX(), $questionY + 56);
//                                    $fpdf->MultiCell(170, 7, $fpdf->GetX(), 0, 'L');
////                                    if($initialStringLength > 170){
////                                        
////                                        $initialStringLength/170;
////                                    }
//                                    
//                                    
//                                    $fpdf->SetXY(20+ $initialStringLength, $questionY);
//                                    $fpdf->SetFont('', 'U');
//                                    $answerString = substr($questionText,$blankIndexStart+2,$blankIndexEnd-$blankIndexStart-2);
//                                    $fpdf->MultiCell(170, 7,$answerString , 0, 'L');
//                                    $answerStringLength = $fpdf->GetStringWidth($answerString);
//                                    
//                                    $fpdf->SetFont('msjh', '', 14);
//                                    
//                                     
//                                    $fpdf->SetXY(20+$initialStringLength+$answerStringLength,$questionY);
//                                    
//                                    $laterString = substr($questionText,$blankIndexEnd+2);
//                                     $fpdf->MultiCell(170, 7,$questionLastString , 0, 'L');
                                    $currentY = $fpdf->GetY() + 5;
                                }
                            }

                            //In case of multiple choice question type
                            if (isset($question['mc'])) {

                                $columns = 1;
                                if (isset($question['mc']['num']) AND is_numeric($question['mc']['num'])) {
                                    $columns = $question['mc']['num'];
                                }

                                $optionWidth = round(150 / ($columns));

                                if (isset($question['mc']['option']) AND is_array($question['mc']['option'])) {
                                    $optionsArray = $question['mc']['option'];

                                    $optionsX = 20;

                                    for ($optionIndex = 0; $optionIndex < sizeof($optionsArray); $optionIndex++) {

                                        $columnNumber = ($optionIndex % $columns) + 1;

                                        $optionsX += (($columnNumber - 1) * $optionWidth) + 5;

                                        $fpdf->SetXY($optionsX, $currentY);
                                        $bulletStyle = "D";
                           
                                        if (strpos($optionsArray[$optionIndex], "{{") !== FALSE AND $isTeacherCopy) {
                                            $bulletStyle = "F";
                                        }
                                        $optionsArray[$optionIndex] = str_replace("{{", "", $optionsArray[$optionIndex]);
                                        $optionsArray[$optionIndex] = str_replace("}}", "", $optionsArray[$optionIndex]);
                                        $fpdf->Circle($optionsX - 2, $currentY + 3.5, 1, $bulletStyle);
                                        $fpdf->MultiCell($optionWidth, 7, $optionsArray[$optionIndex], 0, 'L');

                                        if ($columnNumber % $columns == 0) {
                                            $optionsX = 20;
                                            $currentY += 7;
                                        }
                                    }
                                }
                            }


                            $estimatedHeight = 0;
//                            $estimatedHeight = $fpdf->getStringHeight (200, $question['question_text']);
                            if (isset($question['question_text'])) {
                                $estimatedHeight = $this->getStringHeight($fpdf, 200, 5, $question['question_text']);
                            }
                            $question_image_url = "";
                            if (isset($question['image'])) {
                                $question_image_url = $question['image'];
                            }
                            if (isset($question_image_url) AND $question_image_url != "" AND ! filter_var($question_image_url, FILTER_VALIDATE_URL)) {
                                return response(json_encode(array("error" => "Invalid question image")))->header('Content-Type', 'application/json');
                            }
                            $answer_array = array();
                            if (isset($question['answer'])) {
                                $answer_array = $question['answer'];
                            }
                            if ($question_image_url != "") {
                                $imgAttrArray = getimagesize($question_image_url);

                                $imageWidthInPixel = $imgAttrArray[0];
                                $imageHeightInPixel = $imgAttrArray[1];
//                              $imgRatio = $imgAttrArray[0] / $imgAttrArray[1];
                                $imageWidth = $imageWidthInPixel / 3.78;
                                $imageHeight = $imageHeightInPixel / 3.78;
                                if ($imageWidth > 200) {
                                    $imageWidthResized = 200;
                                    $imageHeightResized = ($imageHeight / $imageWidth) * 200;
                                } else {
                                    $imageWidthResized = $imageWidth;
                                    $imageHeightResized = $imageHeight;
                                }
                                $estimatedHeight += $imageHeightResized;
                            }

                            $estimatedHeight += sizeof($answer_array) * 5;
                            // Answers will have a heading of height 5
                            $estimatedHeight += 5;

                            if ($fpdf->CheckPageBreak($estimatedHeight)) {
                                $currentY = 50;
                                $fpdf->AddPage();
                                array_push($actualPageIndexArray, $actualPDFPageIndex);
                                $actualPDFPageIndex++;
                            }
                            $fpdf->SetXY(10, $currentY);
                            $question['x'] = 10;
                            $question['y'] = $currentY;
                            $question_number = "";
                            if (isset($question['question_no'])) {
                                $question_number = $question['question_no'];
                            }
                            $question_text = "";
                            if (isset($question['question_text'])) {
                                $question_text = $question['question_text'];
                            }
//                        $question_type = $questions['question_type'];

                            if ($question_number != "") {
                                $question_number . ") ";
                            }
                            if ($question_text != "") {
                                $fpdf->MultiCell(200, 5, $question_number . $question_text, 0, 'L');
                                $currentY = $fpdf->GetY() + 5;
                            }


                            $imageHeightResized = 0;
                            if (isset($question_image_url)
                                    AND $question_image_url != ""
                                    AND $question_image_url != NULL) {
                                $fpdf->Image($question_image_url, 10, $currentY, $imageWidthResized, $imageHeightResized);
                            }
                            $currentY += $imageHeightResized;

                            if (sizeof($answer_array)) {
                                $fpdf->SetXY(10, $currentY);
                                $fpdf->MultiCell(200, 5, "Answers: ", 0, 'L');
                                $currentY = $fpdf->GetY() + 3;
                            }

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
                            $questionCount++;
                        }
                        $responseArray['page_group']['page'][$pageCOunt]['main']['section'][$sectionCount]['question'] = $questionsRespoonseArray;

                        $sectionCount++;
                    }
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
//                        var_dump($overlay['url']);

                        if (isset($overlay['url']) && $overlay['url'] != "") {
                            // display of overlay image
                            $fpdf->Image($image_path, $image_x, $image_y, $image_w, $image_h);
                        }
                    }
                    if ($overlay_type == "text") {
                        $overlay_text = $overlay['string'];
                        $text_x = $overlay['x'];
                        $text_y = $overlay['y'];
                        $text_w = $overlay['w'];
                        $text_h = $overlay['h'];

                        // Set font size based on w and h 
                        if ($text_w != "" && $text_h != "") {
                            $fpdf->MultiCell($text_w, $text_h, $overlay_text, 0, 'C');
                        }
                    }

                    // TODO : for overlay type shape.
                }


                // display image only if image exist.
                if (isset($image_path) && $image_path != "" && $image_path != NULL) {
                    
                }

                // display text only if text exist.
                if (isset($overlay_data[0]['string']) && $overlay_data[0]['string'] != "" && $overlay_data[0]['string'] != NULL) {
                    
                }
                $responseArray['page_group']['page'][$pageCOunt]['actual_page_index_array'] = $actualPageIndexArray;
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

        $pdf_name = uniqid() . ".pdf";
//        $pdf_name = "test" . ".pdf";
        if (!file_exists(public_path('pdfs'))) {
            mkdir(public_path('pdfs'), 0777, true);
        }
//        $pdf_path = public_path('test' . DIRECTORY_SEPARATOR . $pdf_name);
        $pdf_path = public_path('pdfs' . DIRECTORY_SEPARATOR . $pdf_name);
        $fpdf->Output($pdf_path, 'F');
//        echo $pdf_name;
//        exit();
        // upload to GCS
        $gcs_result = GCS_helper::upload_to_gcs('pdfs/' . $pdf_name);
        if (!$gcs_result) {
            $responseArray['error'] = "Error in upload of GCS";
            return $responseArray;
        }

        // generate images
        if (!file_exists(public_path('pdf_images'))) {
            mkdir(public_path('pdf_images'), 0777, true);
        }
        $im = new Imagick($pdf_path);
        $page_count = $im->getNumberImages();
        $im->destroy();
        $preview_image_array = array();
        for ($pageIndex = 0; $pageIndex < $page_count; $pageIndex++) {

            $pdf_img = new Imagick();
            $pdf_img->setresolution(210, 297);
            $pdf_img->readimage($pdf_path . "[" . $pageIndex . "]");
//            $pdf_img = new Imagick($pdf_path . "[" . $pageIndex . "]");
            $pdf_img->setImageFormat('jpg');
            $image_name = $pdf_name . "pdf_image_" . $pageIndex . ".jpg";
            $gcs_path = "pdf_images" . DIRECTORY_SEPARATOR . $image_name;
            $image_path = public_path($gcs_path);
            $pdf_img->writeImage($image_path);
            $gcs_result = GCS_helper::upload_to_gcs($gcs_path);

            //upload image to GCS
            if (!$gcs_result) {
                $responseArray['error'] = "Error in upload of GCS";
                return $responseArray;
            }
            unlink($image_path);
            $preview_image_array[] = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $image_name;
            $pdf_img->destroy();
        }
        // delete your local pdf file here
        unlink($pdf_path);

        $pdf_url = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $pdf_name;
        $responseArray['preview_url'] = $pdf_url;
        $responseArray['preview_image_array'] = $preview_image_array;
//        $responseArray['preview_url'] = "custom_url";

        return json_encode($responseArray);
    }

    public function getStringHeight($fpdf, $width, $lineHeight, $text) {
        $tempPdf = clone $fpdf;
        $currentY = $tempPdf->GetY();

        $tempPdf->MultiCell($width, $lineHeight, $text, 0, 'L');

        $finalY = $tempPdf->GetY();
        return $finalY - $currentY;
    }

    static public function create_page($fpdf, $page) {

        // create new blank page
//        $fpdf->AddPage();
        // displaying background image

        $currentBookIndex = $page['current_book_index'];
        $pageIndexArray[] = $currentBookIndex;
        $background_data_array = $page->background;


        foreach ($background_data_array as $background_data) {



            if ($background_data['type'] == 'image') {
                $background_image_path = $background_data['url'];
                if (getimagesize($background_image_path) === false) {
                    return response(json_encode(array("error" => "Invalid background image")))->header('Content-Type', 'application/json');
                }
                // display image only if image exist.
                if (isset($background_image_path) && $background_image_path != "" && $background_image_path != NULL) {
                    $bg_image_x = $background_data['x'];
                    $bg_image_y = $background_data['y'];
                    $bg_image_w = $background_data['w'];
                    $bg_image_h = $background_data['h'];

                    if (file_exists($background_image_path)) {
                        $fpdf->Image($background_image_path, $bg_image_x, $bg_image_y, $bg_image_w, $bg_image_h);
                    }
                }
            }
            if ($background_data['type'] == 'text') {

                // display text only if text exist.
                $background_text = $background_data['string'];
                $text_x = $background_data['x'];
                $text_y = $background_data['y'];
                $text_w = $background_data['w'];
                $text_h = $background_data['h'];
                //TODO Set font size based on w and h 

                if ($text_w != "" && $text_h != "") {
                    $fpdf->SetXY($text_x, $text_y);
                    $fpdf->MultiCell($text_w, $text_h, $background_text, 0, 'C');
                }
            }
        }

        $main_model = $page->main_details;

        $page_header_text = $main_model->header_text;
        // define pdf header here
        $fpdf->MultiCell(200, 10, $page_header_text, 0, 'C');

        $page_footer_text = $main_model->footer_text;
        // define pdf footer here

        $page_section_array = $main_model->section;

        $sectionCount = 0;


        foreach ($page_section_array as $section) {

            $section_instruction_text = $section->instruction_text;
            // display section instruction
            $fpdf->SetXY(10, $fpdf->GetY() + 5);
            $fpdf->MultiCell(200, 5, $section_instruction_text, 0, 'C');
            $section_question_array = $section->question;

//                    if (sizeof($section_question_array) == 0) {
//                        $isValidJson = FALSE;
//                    }

            $currentY = $fpdf->GetY() + 5;

            $questionsRespoonseArray = array();

            foreach ($section_question_array as $question) {

                $fpdf->SetXY(10, $currentY);
                $question['x'] = 10;
                $question['y'] = $currentY;
                $question_number = $question->question_no;
                $question_text = $question->question_text;
//                        $question_type = $questions['question_type'];
                $question_image_url = $question->image;

                $fpdf->MultiCell(200, 5, $question_text, 0, 'L');
                $currentY = $fpdf->GetY() + 5;
                if (getimagesize($question_image_url) === false) {
                    return response(json_encode(array("error" => "Invalid question image")))->header('Content-Type', 'application/json');
                }
                if (file_exists($question_image_url)) {
                    $imgAttrArray = getimagesize($question_image_url);

                    $imgRatio = $imgAttrArray[0] / $imgAttrArray[1];
                }

                if (isset($question_image_url) AND $question_image_url != "" AND $question_image_url != NULL
                        AND file_exists($question_image_url)) {
                    $fpdf->Image($question_image_url, 10, $currentY, 100, 20);
                }
                $currentY += 20;
                $fpdf->SetXY(10, $currentY);
                $fpdf->MultiCell(200, 5, "Answers: ", 0, 'L');
                $currentY = $fpdf->GetY() + 3;

                $answer_array = $question->answer;
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

            $sectionCount++;
        }

        //Overlay Data

        $overlay_data = $page->overlay;

        // fectching only image part of overlay array

        foreach ($overlay_data as $overlay) {
            $overlay_type = $overlay['type'];
            if ($overlay_type == "image") {
                $image_path = $overlay['url'];
                if (getimagesize($image_path) === false) {
                    return response(json_encode(array("error" => "Invalid Overlay image")))->header('Content-Type', 'application/json');
                }
                $image_x = $overlay['x'];
                $image_y = $overlay['y'];
                $image_w = $overlay['w'];
                $image_h = $overlay['h'];

                // display of overlay image
                if (file_exists($image_path)) {
                    $fpdf->Image($image_path, $image_x, $image_y, $image_w, $image_h);
                }
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

        $resultPdf = array(
            '$fpdf' => $fpdf,
            'pageIndexArray' => $pageIndexArray
        );

        return $resultPdf;
    }

    static public function generate_book($input_json) {
        // temprorily reading it from file
//        $json_data = file_get_contents(url('book.json'));
        $book_data_array = json_decode($input_json, true);

        $fpdf = new tFPDF();
//        $fpdf->AddFont('msjh', '', 'msjh.ttf', true);
        $fpdf->AddFont('msjhb', '', 'msjhb.ttf', true);
        $fpdf->AddFont('msjh', '', 'msjh.ttf', true);
        $fpdf->SetFont('msjhb', '', 18);
        $actualPDFPageIndex = 0;
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
        if (getimagesize($cover_img_url) === false) {
            return response(json_encode(array("error" => "Invalid cover image")))->header('Content-Type', 'application/json');
        }
        $fpdf->Image($cover_img_url, 5, $fpdf->GetY(), 200, 270);
        if (isset($book_data_array['cover']['school_logo']) AND $book_data_array['cover']['school_logo'] != "") {
            $school_logo = $book_data_array['cover']['school_logo'];
            if (getimagesize($school_logo) === false) {
                return response(json_encode(array("error" => "Invalid school logo image")))->header('Content-Type', 'application/json');
            }
            $fpdf->Image($school_logo, 25, 250, 30, 25);
        }
        $school_name = $book_data_array['cover']['school_name'];
        $fpdf->SetFont('msjh', '', 15);
        $fpdf->SetXY(55, 265);
        $fpdf->MultiCell(140, 5, $school_name, 0, 'L');


        //Table of contents
        $toc_array = $book_data_array['toc'];

        $fpdf->AddPage();
        $actualPDFPageIndex++;
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
            if ($fpdf->CheckPageBreak(30)) {
                $fpdf->AddPage();
                $tocy = 30;
                $actualPDFPageIndex++;
            }
            $fpdf->SetXY(5, $tocy);
        }

        $page_array = $book_data_array['page'];

        foreach ($page_array AS $page_details) {
            $pageIndex = 0;
            $actualPageIndexArray = array();
            array_push($actualPageIndexArray, $actualPDFPageIndex);
            $fpdf->AddPage();
            $actualPDFPageIndex++;
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

            $author_info_array = array();
            if (isset($page_details['author'])) {
                $author_info_array = $page_details['author'];
            }
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

            // page details

            $page_data = PageModel::get_page_details($page_details['_id']);
            $page_data['current_book_index'] = $actualPDFPageIndex;
            $fpdf->SetXY(20, 20);
            $page_fpdf = Pdf_helper::create_page($fpdf, $page_data);
//            $page_details['page_image_indexs']=$page_fpdf['pageIndexArray'];
            $book_data_array['page'][$pageIndex]['page_image_indexs'] = $page_fpdf['pageIndexArray'];
//            $page_data_array['page'][] = $page_details;
            $pageIndex++;
        }


//        $fpdf->Output('sample_book.pdf', 'I');
        $pdf_name = "book-" . uniqid() . ".pdf";
        if (!file_exists(public_path('pdfs'))) {
            mkdir(public_path('pdfs'), 0777, true);
        }
        $pdf_path = public_path('pdfs' . DIRECTORY_SEPARATOR . $pdf_name);
        $fpdf->Output($pdf_path, 'F');

        // upload to GCS
        $gcs_result = GCS_helper::upload_to_gcs('pdfs/' . $pdf_name);
        if (!$gcs_result) {
            $responseArray['error'] = "Error in upload of GCS";
            return $responseArray;
        }
        // generate images
        if (!file_exists(public_path('pdf_images'))) {
            mkdir(public_path('pdf_images'), 0777, true);
        }
        $im = new Imagick($pdf_path);
        $page_count = $im->getNumberImages();
        $im->destroy();
        $preview_image_array = array();
        for ($pageIndex = 0; $pageIndex < $page_count; $pageIndex++) {

            $pdf_img = new Imagick();
            $pdf_img->setresolution(210, 297);
            $pdf_img->readimage($pdf_path . "[" . $pageIndex . "]");
//            $pdf_img = new Imagick($pdf_path . "[" . $pageIndex . "]");
            $pdf_img->setImageFormat('jpg');
            $image_name = $pdf_name . "pdf_image_" . $pageIndex . ".jpg";
            $gcs_path = "pdf_images" . DIRECTORY_SEPARATOR . $image_name;
            $image_path = public_path($gcs_path);
            $pdf_img->writeImage($image_path);
            $gcs_result = GCS_helper::upload_to_gcs($gcs_path);

            //upload image to GCS
            if (!$gcs_result) {
                $responseArray['error'] = "Error in upload of GCS";
                return $responseArray;
            }
            unlink($image_path);
            $preview_image_array[] = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $image_name;
            $pdf_img->destroy();
        }
        // delete your local pdf file here
        unlink($pdf_path);

        $pdf_url = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $pdf_name;
        $book_data_array['preview_url'] = $pdf_url;
        $book_data_array['preview_image_array'] = $preview_image_array;

        return $book_data_array;
    }

}
