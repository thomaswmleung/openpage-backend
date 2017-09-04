<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Helpers\GCS_helper;
use App\QuestionsModel;
use App\SectionModel;
use App\MainModel;
use App\PageModel;
use App\PageGroupModel;
use App\Helpers\Pdf_helper;
use Illuminate\Support\Facades\Log;

/*
 *  Class Name : PageGroupController
 *  Description : This controller handles parsing of JSON DATA and save to DB
 * 
 * 
 */

class PageGroupController extends Controller {
    /**
     * @SWG\Get(path="/page_group",
     *   tags={"page_group"},
     *   summary="Returns list of page group",
     *   description="Returns page group data",
     *   operationId="get_page_group",
     *   produces={"application/json"},
     *   parameters={},
     *   @SWG\Parameter(
     *     name="skip",
     *     in="query",
     *     description="this is offset or skip the records",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of records to be retrieved ",
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */

    /**
     * @SWG\Get(path="/page_group/{pid}",
     *   tags={"page_group"},
     *   summary="Returns page group data",
     *   description="Returns page group data",
     *   operationId="get_page_group",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="pid",
     *     in="path",
     *     description="ID of the page group that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid media id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function get_page_group(Request $request) {
        $pageGroupModel = new PageGroupModel();
        if (isset($request->pid) && $request->pid != "") {
            $page_group_id = $request->pid;
            $page_group_details = $pageGroupModel->find_page_group_details($page_group_id);
            if ($page_group_details == NULL) {
                $error['error'] = array("Invalid id");
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_page_group_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_page_group_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $page_group_details, "errors" => array());
                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
            }
        } else {
            $search_key = "";
            if (isset($request->search_key)) {
                $search_key = $request->search_key;
            }
            $skip = 0;
            if (isset($request->skip)) {
                $skip = (int) $request->skip;
            }
            $limit = config('constants.default_query_limit');
            if (isset($request->limit)) {
                $limit = (int) $request->limit;
            }
            $query_details = array(
                'search_key' => $search_key,
                'limit' => $limit,
                'skip' => $skip
            );

            $page_group_details = $pageGroupModel->page_group_details($query_details);
            $total_count = $pageGroupModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $page_group_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/page_group",
     *   tags={"page_group"},
     *   summary="Create a page group",
     *   description="",
     *   operationId="create_page_group",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="page group json input",
     *     required=true,
     *     @SWG\Schema()
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Invalid data"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */

    /**
     * @SWG\Put(path="/page_group",
     *   tags={"page_group"},
     *   summary="Create a page group",
     *   description="",
     *   operationId="create_page_group",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="page group json input",
     *     required=true,
     *     @SWG\Schema()
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Invalid data"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function create_page_group(Request $request) {

        $json_data = $request->getContent();


//       return response(json_encode($json_data), 200);
        $page_data_array = json_decode($json_data, true);

        if ($page_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")));
        }

        $page_group_id = $page_data_array['_id'];
        $page_group_model = new PageGroupModel();

        if (isset($page_data_array['_id']) && $page_data_array['_id'] == "") {
            $page_group_id = $page_group_model->create_page_group();
            $page_data_array['_id'] = $page_group_id;
        }


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

                    $main_id = "";
                    if (isset($page['main'])) {
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

                                $question_id = "";
                                if (isset($question['question_id']) AND $question['question_id'] != "") {
                                    $question_id = $question['question_id'];
                                }

                                $question_id = $this->create_question($insert_data, $question_id);


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


                            $section_id = "";
                            if (isset($section['section_id']) && $section['section_id'] != "") {

                                $section_id = $section['section_id'];
                            }

                            $section_id = $this->create_section($insert_data, $section_id);


                            array_push($section_ids, $section_id);
                        }

                        $main_header_text = $main['header_text'];
                        $main_footer_text = $main['footer_text'];

                        $insert_main_data = array(
                            'header_text' => $main_header_text,
                            'footer_text' => $main_footer_text,
                            'section' => $section_ids
                        );




                        $main_id = "";
//                    if (isset($main['main_id']) && $main['main_id'] != "") {
//                        // get page_id from main collection
//                        $main_id = $main['main_id'];
//                    }
                        if (isset($page['page_id']) && $page['page_id'] != "") {
                            // get page_id from main collection
                            $page_id = $page['page_id'];
                            $pageModel = new PageModel();
                            $main_id = $pageModel->fetch_main_id($page_id);
                        }

                        $main_id = $this->create_main($insert_main_data, $main_id);
                    }
                    $ovelay_data = $page['overlay'];
                    /*  $page_ovelay_id = $this->create_overlay($ovelay_data); */

                    $back_ground_data = $page['background'];
                    /*  $page_back_ground_id = $this->create_background($back_ground_data); */

                    $page_remark = $page['remark'];

                    $page_preview_index_array = array();
                    $page_preview_url_array = array();
                    if (isset($page['actual_page_index_array']) && isset($page_data_array['preview_image_array'])) {
                        $page_preview_index_array = $page['actual_page_index_array'];
                        foreach ($page_preview_index_array as $actualPageIndex) {
                            if (isset($page_data_array['preview_image_array'][$actualPageIndex])) {
                                array_push($page_preview_url_array, $page_data_array['preview_image_array'][$actualPageIndex]);
                            }
                        }
                    }
                    $insert_page_data = array(
                        'overlay' => $ovelay_data,
                        'main_id' => $main_id,
                        'background' => $back_ground_data,
                        'remark' => $page_remark,
                        'preview_images' => $page_preview_url_array
                    );

                    $page_id = "";
                    if (isset($page['page_id']) && $page['page_id'] != "") {

                        $page_id = $page['page_id'];
                    }

                    $page_id = $this->create_page($insert_page_data, $page_id);

                    array_push($page_ids, $page_id);
                }

                $page_group_insert_data = array(
                    'page' => $page_ids,
                    'preview_url' => $page_data_array['preview_url'],
                    'preview_image_array' => $page_data_array['preview_image_array'],
                );

                $pageGroup_result = $page_group_model->update_page_group($page_group_insert_data, $page_group_id);
            } else {
                
            }

            $response_array['preview_url'] = $page_data_array['preview_url'];
            $response_array['preview_image_array'] = $page_data_array['preview_image_array'];

            $response_array['success'] = TRUE;
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
//            return json_encode($response_array);
        }
    }

    function create_question($insert_data, $question_id) {
        $questionModel = new QuestionsModel();
        $questionDetails = $questionModel->add_questions($insert_data, $question_id);
        return $questionDetails->_id;
    }

    function create_section($insert_data, $section_id) {
        $sectionModel = new SectionModel();
        $sectionDetails = $sectionModel->add_section($insert_data, $section_id);
        return $sectionDetails->_id;
    }

    function create_main($insert_data, $main_id_) {
        $mainModel = new MainModel();
        $model_details = $mainModel->add_main($insert_data, $main_id_);
        return $model_details->_id;
    }

    function create_page($insert_page_data, $page_id_) {
        $pageModel = new PageModel();
        $page_result = $pageModel->add_or_update_page($insert_page_data, $page_id_);
        return $page_result->_id;
    }

    /**
     * @SWG\Delete(path="/page_group",
     *   tags={"page_group"},
     *   summary="delete page group data",
     *   description="Delete page group from system",
     *   operationId="delete_page_group",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="pid",
     *     in="query",
     *     description="ID of the page group that needs to be deleted",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=400, description="Invalid data supplied"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    function delete_page_group(Request $request) {
        $page_group_id = trim($request->pid);

        $pageGroupModel = new PageGroupModel();
        $page_group_data = $pageGroupModel->page_group_details(array('_id' => $page_group_id));
        if ($page_group_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_page_group_id'),
                    "ERR_MSG" => config('error_messages' . "." .
                            config('error_constants.invalid_page_group_id'))));

            $response_array = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
        }
        $data = explode("/", $page_group_data['preview_url']); // fetching file name from URL
        $objectName = end($data);
        $gcs_result = GCS_helper::delete_from_gcs($objectName);
        if ($gcs_result) {
            PageGroupModel::destroy($page_group_id);
            $error_messages = array(array("ERR_CODE" => config('error_constants.page_group_deleted_success'),
                    "ERR_MSG" => config('error_messages' . "." .
                            config('error_constants.page_group_deleted_success'))));

            $response_array = array("success" => TRUE);
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        } else {
            $error['error'] = array("success" => FALSE, "error" => "Something went wrong");
            return response(json_encode($error), 400)->header('Content-Type', 'application/json');
        }
    }

}
