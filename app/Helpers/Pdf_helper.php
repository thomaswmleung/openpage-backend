<?php

namespace App\Helpers;

use App\Helpers\tFPDF;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\ServiceBuilder;
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
                            $responseAnswersArray = $answer;
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
        if (!file_exists(public_path('pdfs'))) {
            mkdir(public_path('pdfs'), 0777, true);
        }
        $pdf_path = public_path('pdfs' . DIRECTORY_SEPARATOR . $pdf_name);
        $fpdf->Output($pdf_path, 'F');

        // upload to GCS
        $gcs_result = $this->upload_to_gcs('pdfs/' . $pdf_name);
        if (!$gcs_result) {
            $responseArray['error'] = "Error in upload of GCS";
            return $responseArray;
        }
        // delete your local pdf file here
        unlink($pdf_path);
        
        $pdf_url = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $pdf_name;
        $responseArray['preview_url'] = $pdf_url;
      
        return $responseArray;
    }

    public function upload_to_gcs($file_path) {
        $gcloud = new ServiceBuilder([
            'keyFilePath' => Config::get('constants.gcs_key'),
            'projectId' => Config::get('constants.gcs_bucket_name')
        ]);

        // Fetch an instance of the Storage Client
        $storage = $gcloud->storage();

        $bucket = $storage->bucket(Config::get('constants.gcs_bucket_name'));

        // Upload a file to the bucket.
        try {
            $bucket->upload(
                    fopen($file_path, 'r'), [
                'predefinedAcl' => 'publicRead'
                    ]
            );
        } catch (Exception $e) {
            return FALSE;
        }

        return TRUE;
    }

}
