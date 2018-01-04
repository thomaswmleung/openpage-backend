<?php

namespace App\Helpers;

use App\Helpers\tFPDF;
use App\Helpers\GCS_helper;
use Illuminate\Support\Facades\Config;
use App\PageModel;
use Imagick;

class Pdf_helper {

    public function generate_pdf_from_json($json_data, $isTeacherCopy = FALSE) {

        $page_data_array = json_decode($json_data, true);
        $isValidJson = TRUE;

        $fpdf = new tFPDF();
        $fpdf->AddFont('msjhb', '', 'msjhb.ttf', true);
        $fpdf->AddFont('msjh', '', 'msjh.ttf', true);
        $fpdf->SetFont('msjh', '', 14);
        $fpdf->SetAutoPageBreak(false);
        $responseArray = $page_data_array;

        $isPdfEmpty = TRUE;

        if (isset($page_data_array['page_group']['page']) || isset($page_data_array['page_group']['import_url'])) {

            $pageCOunt = 0;

            if (isset($responseArray['page_group']['import_url']) &&
                    $responseArray['page_group']['import_url'] != null AND ! $isTeacherCopy) {
                $isPdfEmpty = TRUE;
                $filename = basename($responseArray['page_group']['import_url']);

                $uniqueId = uniqid();

                $pdf_path = public_path("tmp" . DIRECTORY_SEPARATOR . $uniqueId . $filename);
                GCS_helper::download_object($filename, $pdf_path);

                $im = new Imagick($pdf_path);

                $count = $im->getNumberImages();

                for ($page_index = 0; $page_index < $count; $page_index++) {
                    $pdf_img = new Imagick();
                    $pdf_img->setresolution(210, 297);
                    $pdf_img->readimage($pdf_path . "[" . $page_index . "]");

                    $image_name_from_file = substr($filename, 0, strpos($filename, "."));
                    $image_name = $uniqueId . $image_name_from_file . $page_index . ".jpg";
                    $image_path = "tmp" . DIRECTORY_SEPARATOR . $image_name;
                    $image_absolute_path = public_path($image_path);


                    $pdf_img->scaleImage(1050, 1485);
                    $pdf_img->setImageFormat('jpg');
                    $pdf_img->setImageCompression(imagick::COMPRESSION_JPEG);
                    $pdf_img->setImageCompressionQuality(100);
                    $pdf_img->setImageCompose(Imagick::COMPOSITE_ATOP);
                    $pdf_img->setImageAlphaChannel(11);
                    $pdf_img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
                    $pdf_img->writeImage($image_absolute_path);

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
                    if (!isset($page_data_array['page_group']['page'])) {
                        $page_data_array['page_group']['page'] = array();
                    }
                    array_push($page_data_array['page_group']['page'], $page_data);
                }
//                file_put_contents (public_path("tmp/test_1.jpg"), $im);
//                $im = new \Imagick(public_path("tmp".DIRECTORY_SEPARATOR.$filename));
//                var_dump(public_path("tmp".DIRECTORY_SEPARATOR."test_1.jpg"));


                unlink($pdf_path);
//                var_dump(sizeof($page_data_array['page_group']['page']));
//                exit();
            }

             if (isset($responseArray['page_group']['teachers_import_url']) &&
                    $responseArray['page_group']['teachers_import_url'] != null && $isTeacherCopy) {
                $isPdfEmpty = FALSE;
                $filename = basename($responseArray['page_group']['teachers_import_url']);

                $uniqueId = uniqid();

                $pdf_path = public_path("tmp" . DIRECTORY_SEPARATOR . $uniqueId . $filename);
                GCS_helper::download_object($filename, $pdf_path);

                $im = new Imagick($pdf_path);

                $count = $im->getNumberImages();

                for ($page_index = 0; $page_index < $count; $page_index++) {
                    $pdf_img = new Imagick();
                    $pdf_img->setresolution(210, 297);
                    $pdf_img->readimage($pdf_path . "[" . $page_index . "]");
//                    $pdf_img = new Imagick($pdf_path . "[" . $page_index . "]");
//                    $pdf_img->setImageFormat('jpg');

                    $image_name_from_file = substr($filename, 0, strpos($filename, "."));
                    $image_name = $uniqueId . $image_name_from_file . $page_index . ".jpg";
                    $image_path = "tmp" . DIRECTORY_SEPARATOR . $image_name;
                    $image_absolute_path = public_path($image_path);


//                    $pdf_img->setResolution(2100, 2970);
//                    $pdf_img->setImageCompression(Imagick::COMPRESSION_JPEG);
//                    $pdf_img->setImageCompressionQuality(100);


                    $pdf_img->scaleImage(1050, 1485);
                    $pdf_img->setImageFormat('jpg');
                    $pdf_img->setImageCompression(imagick::COMPRESSION_JPEG);
                    $pdf_img->setImageCompressionQuality(100);


                    $pdf_img->setImageCompose(Imagick::COMPOSITE_ATOP);
                    $pdf_img->setImageAlphaChannel(11);
                    $pdf_img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
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
                    if (!isset($page_data_array['page_group']['page'])) {
                        $page_data_array['page_group']['page'] = array();
                    }
                    array_push($page_data_array['page_group']['page'], $page_data);
                }
//                file_put_contents (public_path("tmp/test_1.jpg"), $im);
//                $im = new Imagick(public_path("tmp".DIRECTORY_SEPARATOR.$filename));
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

                $this->generate_page($fpdf, $page, $actualPageIndexArray, $actualPDFPageIndex, $pageCOunt, $isTeacherCopy, $responseArray);

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

            $image_name = $pdf_name . "pdf_image_" . $pageIndex . ".jpg";
            $gcs_path = "pdf_images" . DIRECTORY_SEPARATOR . $image_name;
            $image_path = public_path($gcs_path);



            $pdf_img->scaleImage(1050, 1485);
            $pdf_img->setImageFormat('jpg');
            $pdf_img->setImageCompression(imagick::COMPRESSION_JPEG);
            $pdf_img->setImageCompressionQuality(100);


            $pdf_img->setImageCompose(Imagick::COMPOSITE_ATOP);
            $pdf_img->setImageAlphaChannel(11);
            $pdf_img->setImageBackgroundColor('white');
            $pdf_img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
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

    static public function generate_page(&$fpdf, &$page, &$actualPageIndexArray, &$actualPDFPageIndex, &$pageCOunt, &$isTeacherCopy, &$responseArray) {
        // create new blank page
        $fpdf->AddPage();
        $actualPDFPageIndex++;
        // displaying background image

        if (isset($page['background']) AND $page['background'] != "") {
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
        }
        if (isset($page['main'])) {

            $fpdf->SetXY(10, 25);
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
                    $fpdf->SetFont('msjh', '', 18);
//                            $fpdf->MultiCell(200, 5, $section['instruction']['text'], 0, 'L');
                    $fpdf->Write(7, $section['instruction']['text']);
                    $fpdf->SetFont('msjh', '', 14);
                }
//                        var_dump($section['paraBox']);
//                        exit();
                if (isset($section['instruction']['tcNote'])) {
                    $fpdf->SetX($fpdf->GetX() + 5);
                    $fpdf->SetFillColor(197, 197, 197);
                    $fpdf->Write(7, " " . $section['instruction']['tcNote'] . " ", "", 1);
                    $fpdf->SetFillColor(0, 0, 0);
                }
                $fpdf->SetY($fpdf->GetY() + 5);
                if (isset($section['paraBox']['text'])) {

                    if (!is_array($section['paraBox']['text'])) {
                        $paraBoxStartY = $fpdf->GetY() + 5;
                        $fpdf->SetXY(10, $fpdf->GetY() + 5);
                        $fpdf->MultiCell(190, 10, str_replace('\r', "\n", $section['paraBox']['text']), 1, 'L');

                        $paraBoxEndY = $fpdf->GetY();
                        $totalLines = abs(($paraBoxEndY - $paraBoxStartY) / 10);
                        for ($paraBoxLineIndex = 0; $paraBoxLineIndex <= $totalLines; $paraBoxLineIndex += 5) {
                            if ($paraBoxLineIndex != 0) {
                                $fpdf->SetXY(200, ($paraBoxLineIndex * 10) + $paraBoxStartY - 10);
                                $fpdf->MultiCell(10, 10, $paraBoxLineIndex, 0, 'L');
                            }
                        }
//                             $fpdf->SetXY(200, $paraBoxStartY);
//                                $fpdf->MultiCell(10, 10,$totalLines , 0, 'L');

                        $fpdf->SetY($paraBoxEndY);
                    } else {
                        $paraBoxStartY = $fpdf->GetY() + 5;


                        $bulletNum = 1;
                        if ($section['paraBox']['bullet'] == "alphabets") {
                            $bulletNum = "A";
                        }

                        $currentParaBoxTextY = $fpdf->GetY() + 5;
                        foreach ($section['paraBox']['text'] as $text) {

                            $fpdf->SetXY(10, $currentParaBoxTextY);
                            $fpdf->MultiCell(10, 10, $bulletNum . ".", 0, 'L');

                            $fpdf->SetXY(20, $currentParaBoxTextY);
                            $fpdf->MultiCell(180, 10, str_replace('\r', "\n", $text), 0, 'L');

                            $currentParaBoxTextY = $fpdf->GetY() + 5;
                            $bulletNum++;
                        }

                        $paraBoxEndY = $fpdf->GetY();

                        $fpdf->Line(8, $paraBoxStartY, 200, $paraBoxStartY);
                        $fpdf->Line(8, $paraBoxEndY, 200, $paraBoxEndY);
                        $fpdf->Line(8, $paraBoxStartY, 8, $paraBoxEndY);
                        $fpdf->Line(200, $paraBoxStartY, 200, $paraBoxEndY);
                    }
                }
                if (isset($section['optBox'])) {

//                            if (isset($section['type']) AND
//                                    $section['type'] == "rhetoric_study_1") {
//
//                                if (isset($section['optBox']['option'])) {
//                                    $optionsArray = $section['optBox']['option'];
//
//                                    $optionsString = "";
//                                    $optionIndex = 1;
//                                    foreach ($optionsArray as $option) {
//                                        $optionsString .= $option;
//                                        if (sizeof($optionsArray) != $optionIndex) {
//                                            $optionsString .= "      ";
//                                        }
//                                        $optionIndex++;
//                                    }
//                                    
//                                    $fpdf->SetXY(10,$fpdf->GetY());
//                                    
//                                }
//                            } else {





                    $colCount = 2;
                    if (isset($section['optBox']['num'])) {
                        $colCount = $section['optBox']['num'];
                    }
                    $optionsArray = array();
                    if (isset($section['optBox']['option'])) {
                        $optionsArray = $section['optBox']['option'];
                    }
                    $currentY = $fpdf->GetY() + 7;
                    $optionsXInitial = 20;
                    $optionsYInitial = $currentY;

                    $optionWidth = 190 / $colCount - 10;
                    $optionsX = $optionsXInitial;

                    //To calculate the height

                    $estimatedY = $optionsYInitial;
                    for ($optionIndex = 0; $optionIndex < sizeof($optionsArray); $optionIndex++) {

//                               $lines = $fpdf->NbLines($optionWidth, $optionsArray[$optionIndex]);
                        $lineHeight = $this->getStringHeight($fpdf, $optionWidth, 7, $optionsArray[$optionIndex]);
//                                    echo $lineHeight;
//                                    echo "<br/>";
//                                    if ($lineHeight >= 7) {
//
//                                        $estimatedY += $lineHeight;
//                                    }
                        if (($optionIndex + 1) % $colCount == 1) {
                            $estimatedY += 7;
                        }
                    }
//                                var_dump($estimatedY);
//                                var_dump($optionsYInitial);
//                                exit();
                    if (sizeof($optionsArray)) {
                        $r = 255;
                        $g = 255;
                        $b = 255;

                        $bgFormat = "D";
                        if (isset($section['optBox']['background'])) {
                            $hex = $section['optBox']['background'];
                            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
                            $bgFormat = "F";
                        }


                        $fpdf->SetFillColor($r, $g, $b);
                        $fpdf->Rect($optionsXInitial, $optionsYInitial - 3, 170, $estimatedY - $optionsYInitial + 5, $bgFormat);
                        $fpdf->SetFillColor(0, 0, 0);
                    }
                    for ($optionIndex = 0; $optionIndex < sizeof($optionsArray); $optionIndex++) {

                        $fpdf->SetXY($optionsX, $currentY);
                        $fpdf->MultiCell($optionWidth, 7, $optionsArray[$optionIndex], 0, 'L');
                        $optionsX += $optionWidth;

                        if (($optionIndex + 1) % $colCount == 0) {
                            $currentY = $fpdf->GetY();
                            $optionsX = $optionsXInitial;
                        }
                    }

                    $optBoxY = $fpdf->GetY();
//                            }
                }
                if (isset($section['instruction_text'])) {
                    $section_instruction_text = $section['instruction_text'];
                    // display section instruction
                    $fpdf->SetXY(10, $fpdf->GetY() + 5);
                    $fpdf->MultiCell(200, 5, $section_instruction_text, 0, 'C');
                }

                $section_question_array = array();

                if (isset($section['question'])) {
                    $section_question_array = $section['question'];
                }

                $currentY = $fpdf->GetY() + 5;

                $questionsResponseArray = array();
                $questionCount = 1;
                $match_sentence_answers_array = array();
                $match_sentence_questions_array = array();


                $wordMatchQuestionMaxY = 0;

                $currentY = $fpdf->GetY() + 10;

                foreach ($section_question_array as $question) {
                    $isQuestionCountDisplayed = FALSE;

                    if (isset($section['type']) AND
                            $section['type'] == "fill_in_the_blanks_for_images_1") {


                        $questionText = $question['text'];
                        $questionText = str_replace('\r', "\n", $questionText);
                        $blankIndexStart = strpos($questionText, "{{");
                        $blankIndexEnd = strpos($questionText, "}}") - $blankIndexStart + 2;
                        $questionText = substr_replace($questionText, "_________", $blankIndexStart, $blankIndexEnd);


                        $questionY = $fpdf->GetY() + 5;
                        $fpdf->SetXY(10, $questionY);


                        if (!$isQuestionCountDisplayed) {
                            $fpdf->SetFont('msjhb', '', 14);
                            $fpdf->MultiCell(10, 7, $questionCount . " . ", 0, 'L');
                            $fpdf->SetFont('msjh', '', 14);
                        }
                        $fpdf->SetXY(20, $questionY);
                        $fpdf->MultiCell(200, 7, $questionText, 0, 'L');
                        $currentY = $fpdf->GetY() + 5;
                    } else if (isset($section['type']) AND
                            $section['type'] == "fill_in_the_blanks_of_puntuations_1") {


                        $currentY = $fpdf->GetY() + 15;
                        $fpdf->SetY($currentY);
                        $questionText = $question['text'];
//                                $questionText = "This is my {{answer1}} and {{answer2}} and some text";

                        $remainingText = $questionText;
                        $searchFor = "{{";
                        $isEndOfString = FALSE;

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ".");
                        $tempLMargin = $fpdf->lMargin;
                        $tempRMargin = $fpdf->rMargin;
                        $fpdf->lMargin = 20;
                        $fpdf->rMargin = 20;
                        $fpdf->SetXY(20, $currentY);
                        while (strlen($remainingText) > 0) {

                            $pos = strpos($remainingText, $searchFor);

                            if ($pos === FALSE) {
                                $pos = strlen($remainingText);
                                $isEndOfString = TRUE;
                            }
                            $text_to_print = substr($remainingText, 0, $pos);
                            $fpdf->Write(8, $text_to_print);
                            $remainingText = substr($remainingText, strlen($text_to_print) + 2);

                            $answerOptionString = "";
                            if (!$isEndOfString) {
                                $answer = substr($remainingText, 0, strpos($remainingText, "}}"));
                                $fpdf->SetX($fpdf->GetX() + 2);
                                $answerStartX = $fpdf->GetX();
                                $answerStartY = $fpdf->GetY();
                                $answerLength = $fpdf->GetStringWidth($answer);

                                $outterSqWidth = 9;
                                if ($answerLength >= 9) {
                                    $outterSqWidth = $answerLength;
                                }



                                $fpdf->Rect($answerStartX - 0.5, $answerStartY - 1.5, $outterSqWidth, $outterSqWidth);
                                $fpdf->SetFillColor(197, 197, 197);
                                $fpdf->Rect($answerStartX + 0.5, $answerStartY - 0.5, $outterSqWidth - 2, $outterSqWidth - 2, "F");


                                $fpdf->Write(8, "$answer");
                                $fpdf->SetFillColor(0, 0, 0);

                                $answerEndX = $fpdf->GetX() + 5;
                                $fpdf->SetX($answerEndX);



                                $answerEndY = $fpdf->GetY();
                                $remainingText = substr($remainingText, strlen($answer) + 2);
                            } else {
                                $fpdf->Write(7, $remainingText);
                            }
                        }
                    } else if (isset($section['type']) AND
                            $section['type'] == "puntuations_study_1") {
                        $currentY = $fpdf->GetY() + 10;
                        $fpdf->SetY($currentY);
                        $questionText = $question['text'];
//                                $questionText = "This is my {{answer1}} and {{answer2}} and some text";

                        $remainingText = $questionText;
                        $searchFor = "{{";
                        $isEndOfString = FALSE;

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ".");
                        $tempLMargin = $fpdf->lMargin;
                        $tempRMargin = $fpdf->rMargin;
                        $fpdf->lMargin = 20;
                        $fpdf->rMargin = 20;
                        $fpdf->SetXY(20, $currentY);
                        while (strlen($remainingText) > 0) {

                            $pos = strpos($remainingText, $searchFor);

                            if ($pos === FALSE) {
                                $pos = strlen($remainingText);
                                $isEndOfString = TRUE;
                            }
                            $text_to_print = substr($remainingText, 0, $pos);
                            $fpdf->Write(7, $text_to_print);
                            $remainingText = substr($remainingText, strlen($text_to_print) + 2);

                            $answerOptionString = "";
                            if (!$isEndOfString) {
                                $answer = substr($remainingText, 0, strpos($remainingText, "}}"));
                                $fpdf->SetX($fpdf->GetX() + 2);
                                $answerStartX = $fpdf->GetX();
                                $answerStartY = $fpdf->GetY();
                                $answerLength = $fpdf->GetStringWidth($answer);
                                $fpdf->Write(7, "  (  ");
                                $answerOptionString .= $answer . "  ";
                                $fpdf->SetFillColor(0, 0, 0);
                                $answerEndX = $fpdf->GetX() + $answerLength;
                                $fpdf->SetX($answerEndX);
                                $fpdf->Write(7, "  )  ");
                                $answerEndY = $fpdf->GetY();
                                $remainingText = substr($remainingText, strlen($answer) + 2);
                            } else {
                                $fpdf->Write(7, $remainingText);
                            }
                        }




                        $fpdf->lMargin = $tempLMargin;
                        $fpdf->rMargin = $tempRMargin;

                        $cols = 4;

                        if (isset($question['cols']) AND
                                is_numeric($question['cols'])) {

                            $cols = $question['cols'];
                        }


                        $optionStartX = 20;
                        $optionWidth = round(180 / $cols);

                        if (isset($question['mc']['option']) AND is_array($question['mc']['option'])) {
                            $optionsArray = $question['mc']['option'];

                            $optionsX = 20;
                            $optionSlNo = 'A';
                            for ($optionIndex = 0; $optionIndex < sizeof($optionsArray); $optionIndex++) {

                                $colNum = ($optionIndex + 1 ) % ($cols );

                                if ($colNum == 1) {
                                    $currentY = $fpdf->GetY() + 14;
                                }
                                if ($colNum == 0) {
                                    $colNum = $cols;
                                }

                                $optionsX = $optionStartX + (($colNum - 1) * $optionWidth) + 5;

                                $fpdf->SetXY($optionsX, $currentY);



                                $isAnswer = FALSE;
                                if (strpos($optionsArray[$optionIndex], "{{") !== FALSE) {
                                    $isAnswer = TRUE;
                                }

                                if (!$isAnswer) {

                                    $fpdf->MultiCell($optionWidth, 7, $optionSlNo . ".  " . $optionsArray[$optionIndex], 0, 'L');
                                } else {


                                    $fpdf->MultiCell($optionWidth, 7, $optionSlNo . ".  " . $answerOptionString, 0, 'L');

                                    if ($isTeacherCopy) {
                                        $fpdf->SetDash(1, 1);
                                        $fpdf->Circle($optionsX + 3, $currentY + 3.5, 4);
                                        $fpdf->SetDash();
                                    }
                                }
                                $optionSlNo++;
                            }
                        }
                    } else if (isset($section['type']) AND
                            $section['type'] == "fill_in_blanks_with_option_2") {
                        if (isset($question['optBox']['option'])) {

                            $optionBoxString = implode("    ", $question['optBox']['option']);

                            $fpdf->SetXY(10, $currentY);
                            if (!$isQuestionCountDisplayed) {
                                $fpdf->SetFont('msjhb', '', 14);
                                $fpdf->MultiCell(10, 7, $questionCount . " . ", 0, 'L');
                                $fpdf->SetFont('msjh', '', 14);
                            }
                            $fpdf->SetXY(20, $currentY);
                            $fpdf->MultiCell($fpdf->GetStringWidth($optionBoxString) + 5, 7, $optionBoxString, 1, 'L');
                            $isQuestionCountDisplayed = TRUE;

                            $currentY = $fpdf->GetY() + 5;
                        }

//                                $fpdf->SetXY(10, $currentY);
//                                $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY(20, $currentY);

                        $questionText = $question['text'];
//                                $questionText = "This is my {{answer1}} and {{answer2}} and some text";

                        $remainingText = $questionText;
                        $searchFor = "{{";
                        $isEndOfString = FALSE;
                        while (strlen($remainingText) > 0) {

                            $pos = strpos($remainingText, $searchFor);

                            if (!$pos) {
                                $pos = strlen($remainingText);
                                $isEndOfString = TRUE;
                            }
                            $text_to_print = substr($remainingText, 0, $pos);
                            $fpdf->Write(7, $text_to_print);

                            $remainingText = substr($remainingText, strlen($text_to_print) + 2);

                            if (!$isEndOfString) {
                                $answer = substr($remainingText, 0, strpos($remainingText, "}}"));
                                $fpdf->SetX($fpdf->GetX() + 2);
                                $answerStartX = $fpdf->GetX();
                                $answerStartY = $fpdf->GetY();
                                $fpdf->SetFillColor(197, 197, 197);
                                $fpdf->Write(7, "  " . $answer . "  ", "", "F");
                                $fpdf->SetFillColor(0, 0, 0);
                                $answerEndX = $fpdf->GetX();
                                $answerEndY = $fpdf->GetY();
//                                        $fpdf->SetDash(1, 1);

                                $fpdf->Line($answerStartX, $answerStartY + 7, $answerEndX, $answerEndY + 7);
//                                        $fpdf->Ellipse(($answerStartX + ($answerEndX - $answerStartX) / 2) + 0.5, $answerStartY + 3.5, (($answerEndX - $answerStartX) / 2) + 1, 3.75);
//                                        $fpdf->SetDash();
                                $remainingText = substr($remainingText, strlen($answer) + 2);
                            } else {
                                $fpdf->Write(7, $remainingText);
                            }
                        }


                        $currentY = $fpdf->GetY() + 7;
                    } else if (isset($section['type']) AND
                            $section['type'] == "rerwite_sentences_2") {

                        $currentY = $fpdf->GetY() + 7;
                        $question_x = 20;
                        $answer_x = 20;
                        $answer_length = 20;
                        $question_length = 150;
                        $question['text'] = trim($question['text']);
                        $question_text_without_answer = substr($question['text'], 0, strpos($question['text'], "{{"));

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY($question_x, $currentY);
                        $fpdf->MultiCell($question_length, 7, $question_text_without_answer, 0, 'L');

                        $answer = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);
                        $currentY += 7;
                        $fpdf->SetXY($answer_x, $currentY);
                        if ($isTeacherCopy) {
                            $fpdf->SetFillColor(197, 197, 197);
//                                    $fpdf->MultiCell($question_length, 7, $answer, 0, 'L', 1);
                            $tempLMargin = $fpdf->lMargin;
                            $tempRMargin = $fpdf->rMargin;

                            $fpdf->lMargin = 20;
                            $fpdf->rMargin = 20;

                            $fpdf->Write(7, " " . $answer . " ", "", 1);

                            $fpdf->lMargin = $tempLMargin;
                            $fpdf->rMargin = $tempRMargin;
                            $fpdf->SetFillColor(0, 0, 0);
                        }
                        $currentY += 7;

                        $lineCount = 1;
                        if (isset($section['answer']['conNum']) AND $section['answer']['conNum'] != ""
                                AND is_numeric($section['answer']['conNum'])) {
                            $lineCount = $section['answer']['conNum'];
                        }

                        for ($lineIndex = 0; $lineIndex < $lineCount; $lineIndex++) {
                            $fpdf->Line(20, $currentY, 190, $currentY);
                            $currentY += 7;
                        }
                    } else if (isset($section['type']) AND
                            $section['type'] == "rerwite_sentences_1") {
                        $currentY = $fpdf->GetY() + 7;
                        $question_x = 20;
                        $answer_x = 20;
                        $answer_length = 20;
                        $question_length = 150;
                        $question['text'] = trim($question['text']);
                        $question_text_without_answer = substr($question['text'], 0, strpos($question['text'], "{{"));

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY($question_x, $currentY);
                        $fpdf->MultiCell($question_length, 7, $question_text_without_answer, 0, 'L');

                        $answer = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);
                        $currentY += 7;
                        $fpdf->SetXY($answer_x, $currentY);
                        if ($isTeacherCopy) {
                            $fpdf->SetFillColor(197, 197, 197);
//                                    $fpdf->MultiCell($question_length, 7, $answer, 0, 'L', 1);
                            $fpdf->Write(7, " " . $answer . " ", "", 1);
                            $fpdf->SetFillColor(0, 0, 0);
                        }
                        $currentY += 7;
                        $fpdf->Line(20, $currentY, 190, $currentY);
                    } else if (isset($section['type']) AND
                            $section['type'] == "true_and_false_1") {

//                                $isTeacherCopy = false;
                        $currentY = $fpdf->GetY() + 7;
                        $question_x = 20;
                        $answer_x = 175;
                        $answer_length = 20;
                        $question_length = 150;
                        $question['text'] = trim($question['text']);
                        $question_text_without_answer = substr($question['text'], 0, strpos($question['text'], "{{"));

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY($question_x, $currentY);
                        $fpdf->MultiCell($question_length, 7, $question_text_without_answer, 0, 'L');



                        $answer = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);

                        $fpdf->SetXY($answer_x, $currentY);
//                                $fpdf->MultiCell(10, 7, "(  ", 0, 'L');

                        if ($isTeacherCopy) {
                            $fpdf->SetXY($answer_x + 4, $currentY - 1);
                            $fpdf->SetFillColor(197, 197, 197);
//                                $fpdf->MultiCell($answer_length-3, 7,$answer , 0, 1, 'L');
                            $fpdf->MultiCell($fpdf->GetStringWidth($answer) + 2, 8, " ", '', 'L', 1);
                            $fpdf->SetFillColor(0, 0, 0);


                            $fpdf->SetXY($answer_x + 4, $currentY);
//                                $fpdf->SetFillColor(197, 197, 197);
//                                $fpdf->MultiCell($answer_length-3, 7,$answer , 0, 1, 'L');
                            $fpdf->MultiCell($fpdf->GetStringWidth($answer), 7, $answer, '', 'L');
//                                $fpdf->SetFillColor(0, 0, 0);
                        }


                        $fpdf->SetXY($answer_x + $fpdf->GetStringWidth($answer), $currentY);
//                                $fpdf->MultiCell(10, 7, "  )", 0, 'R');


                        $currentY = $fpdf->GetY() + 3;
                    } else if (isset($section['type']) AND
                            $section['type'] == "reconstructing_passages_1") {
                        $currentY = $fpdf->GetY() + 5;

                        $textIndex = 0;
                        $questionText = $question['text'];

                        $isAnswer = FALSE;

                        $myCount = 0;
                        $fpdf->SetY($fpdf->GetY() + 7);
                        while ($textIndex <= strlen($questionText)) {

                            if (!$isAnswer) {
                                $len = strpos($questionText, "{{", $textIndex) - $textIndex;

                                if ($len < 0) {
                                    $len = 0;
                                }

                                $text = substr($questionText, $textIndex, $len);
//                                        $fpdf->SetX($fpdf->GetX()+2);
                                $fpdf->Write(7, $text);
                                $fpdf->SetX($fpdf->GetX() + 2);
                                if (strpos($questionText, "{{", $textIndex) !== FALSE) {
                                    $textIndex = strpos($questionText, "{{", $textIndex) + 2;
                                } else {
                                    break;
                                }
                                $isAnswer = TRUE;
                            } else {


                                $text = substr($questionText, $textIndex, strpos($questionText, "}}", $textIndex) - $textIndex);

                                $answerStartX = $fpdf->GetX();
                                $answerStartY = $fpdf->GetY();
                                $fpdf->SetFillColor(197, 197, 197);



                                $answerEndX = $fpdf->GetX() + $fpdf->GetStringWidth($text);
                                $answerEndX = $answerStartX + 4;

                                $fpdf->Rect($answerStartX - 1, $answerStartY - 1, $answerEndX - $answerStartX + 4, 8);

                                $fpdf->Rect($answerStartX, $answerStartY, $answerEndX - $answerStartX + 2, 6, "F");

                                $postRectX = $fpdf->GetX();
                                $postRectY = $fpdf->GetY();

                                $fpdf->SetXY($answerStartX, $answerStartY);
                                if ($isTeacherCopy) {
                                    $fpdf->Write(7, $text);
                                } else {
                                    $fpdf->Write(7, " ");
                                }


                                $fpdf->SetXY($postRectX + 6, $postRectY);
                                $fpdf->SetFillColor(0, 0, 0);
                                $textIndex = strpos($questionText, "}}", $textIndex) + 2;
                                $isAnswer = FALSE;
                            }
                            $myCount++;
                        }
                    } else if (isset($section['type']) AND
                            $section['type'] == "words_matching_1") {

                        if ($wordMatchQuestionMaxY > $fpdf->GetY()) {
                            $currentY = $wordMatchQuestionMaxY;
                        } else {
                            $currentY = $fpdf->GetY();
                        }


                        $question_cols = 2;

                        if (isset($section['question_cols']) AND
                                is_numeric($section['question_cols'])) {

                            $question_cols = $section['question_cols'];
                        }


                        $questionStartX = 10;
                        $questionWidth = round(180 / $question_cols);

                        $colNum = ($questionCount ) % ($question_cols );

                        if ($colNum == 1) {
                            $currentY = $fpdf->GetY() + 7;
                        }
                        if ($colNum == 0) {
                            $colNum = $question_cols;
                        }

                        $questionX = $questionStartX + (($colNum - 1) * $questionWidth) + 5;
//                                $question['text'] = "my text {{answer}}post text";

                        $questionPreText = substr($question['text'], 0, strpos($question['text'], "{{"));

                        $questionPostText = substr($question['text'], strpos($question['text'], "}}") + 2, strlen($question['text']));

                        $questionText = $questionPreText . $questionPostText;

                        $answer = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);
                        $fpdf->SetXY($questionX, $currentY);
                        if (strpos(trim($question['text']), "{{") == 0) {


                            $tempLMargin = $fpdf->lMargin;
                            $tempRMargin = $fpdf->rMargin;

//                                    if ($questionCount == 2) {
//                                        echo $questionX. "  ". $questionWidth;
//                                        exit();
//                                    }

                            $fpdf->lMargin = $questionX;
                            $fpdf->rMargin = 200 - ($questionX + $questionWidth);

                            $fpdf->Write(7, "$questionCount .  (  ");

                            $fpdf->SetFillColor(197, 197, 197);
                            $fpdf->Write(7, " " . $answer . " ", "", 1);
                            $fpdf->SetFillColor(0, 0, 0);
                            $fpdf->Write(7, "  )");
                            $wordMatchQuestionMaxY = $fpdf->GetY();
                            $fpdf->Write(7, $questionText);
                            if ($fpdf->GetY() > $wordMatchQuestionMaxY) {
                                $wordMatchQuestionMaxY = $fpdf->GetY();
                            }
                            $fpdf->lMargin = $tempLMargin;
                            $fpdf->rMargin = $tempRMargin;
                        } else {
                            $tempLMargin = $fpdf->lMargin;
                            $tempRMargin = $fpdf->rMargin;

//                                    if ($questionCount == 1) {
//                                        echo $questionX. "  ". $questionWidth;
//                                        exit();
//                                    }

                            $fpdf->lMargin = $questionX;
                            $fpdf->rMargin = 200 - ($questionX + $questionWidth);

                            $fpdf->Write(7, "$questionCount ." . $questionText);
                            $wordMatchQuestionMaxY = $fpdf->GetY();
                            $fpdf->Write(7, "  (  ");

                            $fpdf->SetFillColor(197, 197, 197);
                            $fpdf->Write(7, " " . $answer . " ", "", 1);
                            $fpdf->SetFillColor(0, 0, 0);
                            $fpdf->Write(7, "  )");

                            if ($fpdf->GetY() > $wordMatchQuestionMaxY) {
                                $wordMatchQuestionMaxY = $fpdf->GetY();
                            }

                            $fpdf->lMargin = $tempLMargin;
                            $fpdf->rMargin = $tempRMargin;
                        }
//                                $answer = "  " . $answer . "  ";
                    } else if (isset($section['type']) AND ( $section['type'] == "verbs_study_1"
                            OR $section['type'] == "paragraph_comprehension_1")) {

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY(20, $currentY);

                        $questionText = $question['text'];
//                                $questionText = "This is my {{answer1}} and {{answer2}} and some text";

                        $remainingText = $questionText;
                        $searchFor = "{{";
                        $isEndOfString = FALSE;
                        while (strlen($remainingText) > 0) {

                            $pos = strpos($remainingText, $searchFor);

                            if (!$pos) {
                                $pos = strlen($remainingText);
                                $isEndOfString = TRUE;
                            }
                            $text_to_print = substr($remainingText, 0, $pos);
                            $fpdf->Write(7, $text_to_print);

                            $remainingText = substr($remainingText, strlen($text_to_print) + 2);

                            if (!$isEndOfString) {
                                $answer = substr($remainingText, 0, strpos($remainingText, "}}"));

                                $answerStartX = $fpdf->GetX();
                                $answerStartY = $fpdf->GetY();

                                $fpdf->Write(7, $answer);

                                $answerEndX = $fpdf->GetX();
                                $answerEndY = $fpdf->GetY();
                                $fpdf->SetDash(1, 1);
                                $fpdf->Ellipse(($answerStartX + ($answerEndX - $answerStartX) / 2) + 0.5, $answerStartY + 3.5, (($answerEndX - $answerStartX) / 2) + 1, 3.75);
                                $fpdf->SetDash();
                                $remainingText = substr($remainingText, strlen($answer) + 2);
                            } else {
                                $fpdf->Write(7, $remainingText);
                            }
                        }


                        $currentY = $fpdf->GetY() + 10;
                    } else if (isset($section['type']) AND
                            $section['type'] == "exaggeration_study_1") {
                        $question['text'] = trim($question['text']);
                        $question_text_without_answer = substr($question['text'], 0, strpos($question['text'], "{{"));

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY(20, $currentY);
//                                $fpdf->MultiCell($question_length, 7, $question_text_without_answer, 0, 'L');
                        $fpdf->Write(7, $question_text_without_answer);

                        $dashLineStartX = $fpdf->GetX();
                        $dashLineStartY = $fpdf->GetY();

                        $answer = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);
//                                $fpdf->SetFont('', "U");
                        $fpdf->Write(7, $answer);

                        $dashLineEndX = $fpdf->GetX();
                        $dashLineEndY = $fpdf->GetY();


                        while ($dashLineEndY >= $dashLineStartY) {

                            if ($dashLineEndY == $dashLineStartY) {
                                $fpdf->SetDash(1, 1);
                                $fpdf->Line($dashLineStartX + 2, $dashLineStartY + 7, $dashLineEndX + 2, $dashLineEndY + 7);
                                $fpdf->SetDash();
                                break;
                            } else {
                                $fpdf->SetDash(1, 1);
                                $fpdf->Line($dashLineStartX + 2, $dashLineStartY + 7, 190, $dashLineStartY + 7);
                                $fpdf->SetDash();
                                $dashLineStartX = 10;
                            }
                            $dashLineStartY += 7;
                        }








//                                $fpdf->SetFont('msjh', '', 14);
//                                 $fpdf->SetXY($fpdf->, $y)
                        $currentY = $fpdf->GetY() + 7;
                    } else if (isset($section['type']) AND ( $section['type'] == "description_study_1"
                            OR $section['type'] == "rhetoric_study_1")) {

                        $currentY = $fpdf->GetY() + 7;
                        $question_x = 20;
                        $answer_x = 175;
                        $answer_length = 20;
                        $question_length = 150;
                        $question['text'] = trim($question['text']);
                        $question_text_without_answer = substr($question['text'], 0, strpos($question['text'], "{{"));

                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY($question_x, $currentY);
                        $fpdf->MultiCell($question_length, 7, $question_text_without_answer, 0, 'L');



                        $answer = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);
                        $answer = "  " . $answer . "  ";
                        $fpdf->SetXY($answer_x, $currentY);
                        $fpdf->MultiCell(10, 7, "(  ", 0, 'L');

                        if ($isTeacherCopy) {
                            $fpdf->SetXY($answer_x + 4, $currentY - 1);
                            $fpdf->SetFillColor(197, 197, 197);
//                                $fpdf->MultiCell($answer_length-3, 7,$answer , 0, 1, 'L');
                            $fpdf->MultiCell($fpdf->GetStringWidth($answer) + 2, 8, " ", '', 'L', 1);
                            $fpdf->SetFillColor(0, 0, 0);


                            $fpdf->SetXY($answer_x + 4, $currentY);
//                                $fpdf->SetFillColor(197, 197, 197);
//                                $fpdf->MultiCell($answer_length-3, 7,$answer , 0, 1, 'L');
                            $fpdf->MultiCell($fpdf->GetStringWidth($answer), 7, $answer, '', 'L');
//                                $fpdf->SetFillColor(0, 0, 0);
                        }


                        $fpdf->SetXY($answer_x + $fpdf->GetStringWidth($answer), $currentY);
                        $fpdf->MultiCell(10, 7, "  )", 0, 'R');


                        $currentY = $fpdf->GetY() + 3;
                    } else if (isset($section['type']) AND
                            $section['type'] == "match_phrase_1") {

                        $question['text'] = trim($question['text']);
                        $match_phrase_part1 = substr($question['text'], 0, strpos($question['text'], "{{"));
                        $match_phrase_part2 = substr($question['text'], strpos($question['text'], "}}") + 2, strlen($question['text']) - strpos($question['text'], "}}") + 2);

                        $answer = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);
                        $answer = "  " . $answer . "  ";
                        $match_phrase1_x = 20;
                        $match_phrase1_length = 70;

                        $answer_x = 90;
                        $answer_length = 10;

                        $match_phrase2_x = 130;
                        $match_phrase2_length = 70;

                        $currentY = $fpdf->GetY() + 7;
                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY($match_phrase1_x, $currentY);
                        $fpdf->MultiCell($match_phrase1_length, 7, $match_phrase_part1, 0, 'L');

                        if ($isTeacherCopy) {
                            $fpdf->SetXY($answer_x, $currentY);
                            $fpdf->MultiCell(10, 7, "(  ", 0, 'L');

                            $fpdf->SetXY($answer_x + 4, $currentY - 1);
                            $fpdf->SetFillColor(197, 197, 197);
//                                $fpdf->MultiCell($answer_length-3, 7,$answer , 0, 1, 'L');
                            $fpdf->MultiCell($answer_length, 8, " ", '', 'L', 1);
                            $fpdf->SetFillColor(0, 0, 0);


                            $fpdf->SetXY($answer_x + 4, $currentY);
//                                $fpdf->SetFillColor(197, 197, 197);
//                                $fpdf->MultiCell($answer_length-3, 7,$answer , 0, 1, 'L');
                            $fpdf->MultiCell($answer_length, 7, $answer, '', 'L');
//                                $fpdf->SetFillColor(0, 0, 0);




                            $fpdf->SetXY($answer_x + $fpdf->GetStringWidth($answer), $currentY);
                            $fpdf->MultiCell(10, 7, "  )", 0, 'R');
                        }
                        $fpdf->SetXY($match_phrase2_x, $currentY);
                        $fpdf->MultiCell($match_phrase2_length, 7, $match_phrase_part2, 0, 'L');

                        $currentY = $fpdf->GetY();
                    } else if (isset($section['type']) AND
                            $section['type'] == "match_sentences_1") {

                        $question['text'] = trim($question['text']);
                        $match_phrase_part1 = substr($question['text'], 0, strpos($question['text'], "{{"));
//                                $match_phrase_part2 = substr($question['text'], 
//                                        strpos($question['text'], "}}")+2,  strlen($question['text']) 
//                                        - strpos($question['text'], "}}")+2);

                        $match_phrase_part2 = substr($question['text'], strpos($question['text'], "{{") + 2, strpos($question['text'], "}}") - strpos($question['text'], "{{") - 2);
//                                $answer = "  ".$answer."  ";
//                                var_dump($match_phrase_part1);
//                                var_dump($match_phrase_part2);
//                                exit();
                        $answer = "";
                        $match_phrase1_x = 20;
                        $match_phrase1_length = 70;

                        $answer_x = 90;
                        $answer_length = 40;

                        $match_phrase2_x = 130;
                        $match_phrase2_length = 70;

                        $currentY = $fpdf->GetY() + 7;
                        $fpdf->SetXY(10, $currentY);
                        $fpdf->MultiCell(10, 7, $questionCount . ". ", 0, 'L');

                        $fpdf->SetXY($match_phrase1_x, $currentY);
                        $fpdf->MultiCell($match_phrase1_length, 7, $match_phrase_part1, 0, 'L');

                        if ($isTeacherCopy) {
                            $fpdf->SetXY($answer_x, $currentY);
                            $fpdf->SetFillColor(197, 197, 197);

                            $x1 = $answer_x;
                            $y1 = $currentY + 3.5;
                            $fpdf->Circle($x1, $y1, 2, 'F');
                            $fpdf->SetFillColor(0, 0, 0);

                            $fpdf->SetXY($answer_x + $answer_length, $currentY);
                            $fpdf->SetFillColor(197, 197, 197);

                            $x2 = $answer_x + $answer_length - 5;
                            $y2 = $currentY + 3.5;
                            $fpdf->Circle($x2, $y2, 2, 'F');
                            $fpdf->SetFillColor(0, 0, 0);

                            $linesInfo = array('x' => $x1,
                                'y' => $y1,
                                'answer_index' => $question['answer_index']
                            );
                            array_push($match_sentence_questions_array, $linesInfo);

                            $answerLinesInfo = array('x' => $x2,
                                'y' => $y2,
                            );

                            array_push($match_sentence_answers_array, $answerLinesInfo);
                        }
                        $fpdf->SetXY($match_phrase2_x, $currentY);
                        $fpdf->MultiCell($match_phrase2_length, 7, $match_phrase_part2, 0, 'L');

                        $currentY = $fpdf->GetY();
                    } else if (isset($section['type']) AND
                            $section['type'] == "match_phrase_1") {

                        if (isset($question['text'])) {


                            if (!$isTeacherCopy) {
                                $questionText = $question['text'];
                                $questionText = str_replace('\r', "\n", $questionText);
                                $blankIndexStart = strpos($questionText, "{{");
                                $blankIndexEnd = strpos($questionText, "}}") - $blankIndexStart + 2;
                                $questionText = substr_replace($questionText, "_________", $blankIndexStart, $blankIndexEnd);


                                $questionY = $fpdf->GetY() + 5;
                                $fpdf->SetXY(10, $questionY);


                                if (!$isQuestionCountDisplayed) {
                                    $fpdf->SetFont('msjhb', '', 14);
                                    $fpdf->MultiCell(10, 7, $questionCount . " . ", 0, 'L');
                                    $fpdf->SetFont('msjh', '', 14);
                                }
                                $fpdf->SetXY(20, $questionY);
                                $fpdf->MultiCell(200, 7, $questionText, 0, 'L');
                                $currentY = $fpdf->GetY() + 5;
                            } else {




                                $questionText = $question['text'];
                                $questionText = str_replace('\r', "\n", $questionText);
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
                                        $fpdf->SetFont('msjhb', '', 14);
                                        $fpdf->MultiCell(10, 7, $questionCount . " . ", 0, 'L');
                                        $fpdf->SetFont('msjh', '', 14);
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
                                    $fpdf->SetFillColor(197, 197, 197);
                                    $fpdf->MultiCell($fpdf->GetStringWidth($answerString) + 5, 7, $answerString, 'B', 'L', 1);
                                    $fpdf->SetFillColor(0, 0, 0);
                                    $fpdf->SetFont('msjh', '', 14);
                                    $fpdf->Write(7, $questionLastString);

//                                    $this->w = $tempW;
                                    $fpdf->lMargin = $tempLMargin;
                                    $fpdf->rMargin = $tempRMargin;
                                } else {
                                    $fpdf->SetXY(10, $currentY);
//                                    $fpdf->MultiCell(170, 7,$questionText , 0, 'L');
//                                    $fpdf->Ellipse(100, 50, 30, 20,'F');
                                    $fpdf->SetFont('msjhb', '', 14);
                                    $fpdf->MultiCell(10, 7, $questionCount . " . ", 0, 'L');
                                    $fpdf->SetFont('msjh', '', 14);
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
                    } else {
                        if (isset($question['optBox']['option'])) {

                            $optionBoxString = implode("    ", $question['optBox']['option']);

                            $fpdf->SetXY(10, $currentY);
                            if (!$isQuestionCountDisplayed) {
                                $fpdf->SetFont('msjhb', '', 14);
                                $fpdf->MultiCell(10, 7, $questionCount . " . ", 0, 'L');
                                $fpdf->SetFont('msjh', '', 14);
                            }
                            $fpdf->SetXY(20, $currentY);
                            $fpdf->MultiCell($fpdf->GetStringWidth($optionBoxString) + 5, 7, $optionBoxString, 1, 'L');
                            $isQuestionCountDisplayed = TRUE;

                            $currentY = $fpdf->GetY() + 5;
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
                                    $fpdf->SetFillColor(197, 197, 197);
                                    $fpdf->Circle($optionsX - 2, $currentY + 3.5, 2, $bulletStyle);
                                    $fpdf->SetFillColor(0, 0, 0);
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
                            return json_encode(array("error" => "Invalid question image"));
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
                    }
                    $questionsResponseArray[] = $question;
                    $questionCount++;
                }


                if (isset($section['table'])) {
                    $tableArray = $section['table'];

                    $tableStartX = 20;

                    $tableCols = 2;
                    $cellCount = 2;


                    if (isset($section['table_data']['cell_count'])) {

                        $cellCount = $section['table_data']['cell_count'];
                    }
                    if (isset($section['table_data']['num'])) {

                        $tableCols = $section['table_data']['num'];
                    }
                    $tableWidth = 180 / $tableCols;

                    $tableStartY = $currentY;
                    for ($tableIndex = 0; $tableIndex < sizeof($tableArray); $tableIndex++) {

                        $currentY = $tableStartY;
                        $table = $tableArray[$tableIndex];
                        $currentTableCol = ($tableIndex + 1) % $tableCols;
                        if ($currentTableCol == 0) {
                            $currentTableCol = $tableCols;
                        }

                        $offset = ( $currentTableCol - 1) * $tableWidth;

                        if ($offset != 0) {
                            $offset += 5;
                        }
                        $tableX = $tableStartX + $offset;

                        $rows = $table['row'];
                        $rowCount = 0;
                        foreach ($rows as $row) {
                            $rowCount++;
                            $cellWidth = $tableWidth / $cellCount;
                            $cells = $row['cell'];
                            $cellIndex = 0;
                            foreach ($cells as $cellData) {

                                $cellIndex++;
                                if (isset($cellData['colSpan'])) {
                                    $cellWidth *= $cellData['colSpan'];
                                }

                                $r = 0;
                                $g = 0;
                                $b = 0;

                                $isBg = FALSE;
                                if (isset($cellData['background'])) {
                                    $hex = $cellData['background'];
                                    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
                                    $isBg = TRUE;
                                }
                                $cellX = $tableX + ($cellIndex - 1) * $cellWidth;

                                $cellContent = "";

                                if (isset($cellData['text'])) {
                                    $cellContent = $cellData['text'];
                                } else if (isset($cellData['question']['text'])) {
                                    $cellContent = $cellData['question']['text'];
                                }
                                if (strpos($cellContent, "{{") !== FALSE) {
                                    if ($isTeacherCopy) {
                                        $cellContent = str_replace("{{", "", $cellContent);
                                        $cellContent = str_replace("}}", "", $cellContent);
                                    } else {
                                        $cellContent = "";
                                    }
                                }

                                $fpdf->SetXY($cellX, $currentY);
                                $fpdf->SetFillColor($r, $g, $b);
                                $fpdf->MultiCell($cellWidth, 9, $cellContent, 1, 'C', $isBg);
                                $fpdf->SetFillColor(0, 0, 0);
                            }

                            $currentY += 9;
                        }
                    }
                }
                if (isset($section['type']) AND
                        $section['type'] == "match_sentences_1"
                        AND $isTeacherCopy) {

                    foreach ($match_sentence_questions_array as $lineInfo) {
                        $x1 = $lineInfo['x'];
                        $y1 = $lineInfo['y'];

                        $answerIndex = $lineInfo['answer_index'];

                        if (isset($match_sentence_answers_array[$answerIndex - 1])) {
                            $x2 = $match_sentence_answers_array[$answerIndex - 1]['x'];
                            $y2 = $match_sentence_answers_array[$answerIndex - 1]['y'];
                            $fpdf->SetDash(1, 1);
                            $fpdf->Line($x1, $y1, $x2, $y2);
                            $fpdf->SetDash();
                        } else {
                            continue;
                        }
                    }
                }

                $responseArray['page_group']['page'][$pageCOunt]['main']['section'][$sectionCount]['question'] = $questionsResponseArray;

                $sectionCount++;
            }
        }


        if (isset($page['overlay']) AND $page['overlay'] != "") {
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
        }
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
        $book_title = $book_data_array['title'];
        $fpdf->MultiCell(200, 10, $book_title, 0, 'C');
        $fpdf->SetXY(5, $fpdf->GetY());
        $sub_title = $book_data_array['subtitle'];
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

            $exercises_array = array();
            if (isset($toc['exercises'])) {
                $exercises_array = $toc['exercises'];
            }

            foreach ($exercises_array as $exercise) {

                if ($exercise['type'] == 'unit') {
                    $fpdf->SetFillColor(147, 148, 150);
                    $fpdf->MultiCell(205, 10, $exercise['reference_text'], 0, 'L', true);
                    $tocy = $fpdf->GetY();
                } else if ($exercise['type'] == 'inline') {

                    $fpdf->SetFont('msjh', '', 8);
                    $fpdf->SetXY($toc_col1x, $tocy);
                    $fpdf->MultiCell($toc_col1_width, 5, $exercise['reference_text'], 0, 'L');
                    $fpdf->SetFont('msjh', '', 12);
                    $fpdf->SetXY($toc_col2x, $tocy + 0.5);
                    $fpdf->SetFillColor(234, 238, 239);
                    $fpdf->MultiCell($toc_col2_width, 10, $exercise['language_knowledge'], 0, 'L', true);
//            
                    $particular_str = "";
                    foreach ($exercise['particular'] as $particular) {
                        $particular_str .= $particular . "\n";
                    }
                    $fpdf->SetFont('msjh', '', 8);
                    $fpdf->SetXY($toc_col3x, $tocy + 0.5);
                    $fpdf->MultiCell($toc_col3_width, 5, $particular_str, 0, 'L');
                    $fpdf->SetFont('msjh', '', 12);


                    $fpdf->SetXY($toc_col4x, $tocy);
                    $fpdf->MultiCell($toc_col4_width, 10, $exercise['page_code'], 0, 'L');



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
        }

        $page_array = $book_data_array['page'];
        $pageIndex = 0;
        $fpdf->AddPage();
        foreach ($page_array AS $page_details) {
            $actualPageIndexArray = array();
            array_push($actualPageIndexArray, $actualPDFPageIndex);

            $actualPDFPageIndex++;

            // Start of Chapter Number

            $chapter_number_details = $page_details['chapter_number'];

            if (isset($chapter_number_details['position-coordinates'])) {
                $chapter_number_coordinates = $chapter_number_details['position-coordinates'];

                if (!isset($chapter_number_details['position-coordinates']['x'])) {
                    $chapter_number_coordinates['x'] = 5;
                }

                if (!isset($chapter_number_details['position-coordinates']['y'])) {
                    $chapter_number_coordinates['y'] = 5;
                }

                if (!isset($chapter_number_details['position-coordinates']['max-width'])) {
                    $chapter_number_coordinates['max-width'] = 10;
                }
            } else {
                $chapter_number_coordinates = array();
                $chapter_number_coordinates['x'] = 5;
                $chapter_number_coordinates['y'] = 5;
                $chapter_number_coordinates['max-width'] = 10;
            }



            if (isset($chapter_number_details['text'])) {
                $fpdf->SetXY($chapter_number_coordinates['x'], $chapter_number_coordinates['y']);
                $fpdf->SetFont('msjhb', '', 10);
                $fpdf->MultiCell($chapter_number_coordinates['max-width'], 10, $chapter_number_details['text'], 0);
            }

            // End of Chapter Number
            // Start of Chapter Name

            $chapter_name_details = $page_details['chapter-name'];

            if (isset($chapter_name_details['position-coordinates'])) {
                $chapter_name_coordinates = $chapter_name_details['position-coordinates'];

                if (!isset($chapter_name_details['position-coordinates']['x'])) {
                    $chapter_name_coordinates['x'] = 5;
                }

                if (!isset($chapter_name_details['position-coordinates']['y'])) {
                    $chapter_name_coordinates['y'] = 5;
                }

                if (!isset($chapter_name_details['position-coordinates']['max-width'])) {
                    $chapter_name_coordinates['max-width'] = 10;
                }
            } else {
                $chapter_name_coordinates = array();
                $chapter_name_coordinates['x'] = 5;
                $chapter_name_coordinates['y'] = 5;
                $chapter_name_coordinates['max-width'] = 10;
            }



            if (isset($chapter_name_details['text'])) {
                $fpdf->SetXY($chapter_name_coordinates['x'], $chapter_name_coordinates['y']);
                $fpdf->SetFont('msjhb', '', 10);
                $fpdf->MultiCell($chapter_name_coordinates['max-width'], 10, $chapter_name_details['text'], 0);
            }



            //// End of Chapter Name
            // Start of date grade 1

            $date_grade_1_details = $page_details['date_and_grade_1'];

            if (isset($date_grade_1_details['position-coordinates'])) {
                $date_grade_1_coordinates = $date_grade_1_details['position-coordinates'];

                if (!isset($date_grade_1_details['position-coordinates']['x'])) {
                    $date_grade_1_coordinates['x'] = 5;
                }

                if (!isset($date_grade_1_details['position-coordinates']['y'])) {
                    $date_grade_1_coordinates['y'] = 5;
                }

                if (!isset($date_grade_1_details['position-coordinates']['max-width'])) {
                    $date_grade_1_coordinates['max-width'] = 10;
                }
            } else {
                $date_grade_1_coordinates = array();
                $date_grade_1_coordinates['x'] = 5;
                $date_grade_1_coordinates['y'] = 5;
                $date_grade_1_coordinates['max-width'] = 10;
            }



            if (isset($date_grade_1_details['text'])) {
                $fpdf->SetXY($date_grade_1_coordinates['x'], $date_grade_1_coordinates['y']);
                $fpdf->SetFont('msjhb', '', 10);
                $fpdf->MultiCell($date_grade_1_coordinates['max-width'], 10, $date_grade_1_details['text'], 0);
            }



            // end of date grade 1
//            $pdf_name = "test_book.pdf";
//            $pdf_path = public_path('test' . DIRECTORY_SEPARATOR . $pdf_name);
////            $pdf_path = public_path('pdfs' . DIRECTORY_SEPARATOR . $pdf_name);
//            $fpdf->Output($pdf_path, 'F');
//            echo $pdf_name;
//            exit();
//            $fpdf->SetXY(18, 5);
//            $fpdf->MultiCell(180, 10, $page_details['page_title'], 0);
//            $fpdf->SetFont('msjh', '', 12);
//            $fpdf->SetXY(5, 15);
//            $fpdf->MultiCell(8, 5, $page_details['unit'], 0);
//            $fpdf->SetXY(13, 15);
//            $fpdf->MultiCell(8, 5, $page_details['topic'], 0);
//            $fpdf->SetXY(5, 260);
//            $fpdf->MultiCell(8, 5, $page_details['page_number'], 0);
//            $author_info_array = array();
//            if (isset($page_details['author'])) {
//                $author_info_array = $page_details['author'];
//            }
//            $fpdf->SetFont('msjh', '', 8);
//            $authory = 260;
//            foreach ($author_info_array as $author_info) {
//                $fpdf->SetXY(15, $authory);
//                $fpdf->MultiCell(50, 4, $author_info, 0);
//                $authory += 4;
//            }
//            $fpdf->SetXY(180, 260);
//            $fpdf->MultiCell(50, 4, $page_details['royalty'], 0);


            $fpdf->SetFont('msjh', '', 12);

            // page details

            $page_data = PageModel::get_page_details($page_details['_id']);
//            var_dump($page_data);
//            exit();
            $page_data['current_book_index'] = $actualPDFPageIndex;
            $fpdf->SetXY(20, 20);
//            $page_fpdf = Pdf_helper::create_page($fpdf, $page_data);
            $isTeachersCopy = FALSE;

            Pdf_helper::generate_page($fpdf, $page_data, $actualPageIndexArray, $actualPDFPageIndex, $pageIndex, $isTeachersCopy, $responseArray);
//            $page_details['page_image_indexs']=$page_fpdf['pageIndexArray'];
            $book_data_array['page'][$pageIndex]['page_image_indexs'] = $actualPageIndexArray;
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

    public function generate_page_pdf_from_json($page_json, $isTeacherCopy = FALSE) {
        $page = json_decode($page_json, true);
        // create new blank page
        $fpdf = new tFPDF();
        $fpdf->AddPage();
        $responseArray = $page;
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
                $responseArray['main']['section'][$sectionCount]['question'] = $questionsRespoonseArray;

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

        $pdf_name = uniqid() . ".pdf";
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

        return json_encode($responseArray);
    }

}
