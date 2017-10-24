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
use App\KeywordModel;
use App\Helpers\KeywordHelper;
use App\Helpers\Token_helper;

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
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search parameter or key word to search",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="title",
     *     in="query",
     *     description="Filter by title",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="sub_title",
     *     in="query",
     *     description="Filter by sub title",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="created_by",
     *     in="query",
     *     description="Filter created by user id",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="from_date",
     *     in="query",
     *     description="Created at start date(YYYY-mm-dd) ",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="to_date",
     *     in="query",
     *     description="Created at end date(YYYY-mm-dd) ",
     *     type="string"
     *   ),
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
     *   @SWG\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by value",
     *         type="array",
     *         @SWG\Items(
     *             type="string",
     *             enum={"created_at","title", "sub_title"},
     *             default="created_at"
     *         ),
     *         collectionFormat="multi"
     *   ),
     *   @SWG\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Order by Ascending or descending",
     *         type="array",
     *         @SWG\Items(
     *             type="string",
     *             enum={"ASC", "DESC"},
     *             default="DESC"
     *         ),
     *         collectionFormat="multi"
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
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_page_group_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_page_group_id')['error_message']));

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
            $title = "";
            if (isset($request->title)) {
                $title = $request->title;
            }
            $sub_title = "";
            if (isset($request->sub_title)) {
                $sub_title = $request->sub_title;
            }
            $created_by = "";
            if (isset($request->created_by)) {
                $created_by = $request->created_by;
            }
            $from_date = "";
            if (isset($request->from_date)) {
                $from_date = date("Y-m-d H:i:s", strtotime($request->from_date. "00:00:00"));
            }
            $to_date = "";
            if (isset($request->to_date)) {
                $to_date = date("Y-m-d H:i:s", strtotime($request->to_date. " 23:59:59"));
            }
           
            if($from_date=="" || $to_date ==""){
                $from_date="";
                $to_date="";
            }
            
            $skip = 0;
            if (isset($request->skip)) {
                $skip = (int) $request->skip;
            }
            $limit = config('constants.default_query_limit');
            if (isset($request->limit)) {
                $limit = (int) $request->limit;
            }
            $sort_by = 'created_at';
            if (isset($request->sort_by)) {
                $sort_by = $request->sort_by;
            }
            $order_by = 'DESC';
            if (isset($request->order_by)) {
                $order_by = $request->order_by;
            }
            $query_details = array(
                'search_key' => $search_key,
                'title' => $title,
                'sub_title' => $sub_title,
                'created_by' => $created_by,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'limit' => $limit,
                'skip' => $skip,
                'sort_by' => $sort_by,
                'order_by' => $order_by
            );

            $page_group_details = $pageGroupModel->page_group_details($query_details);
            $total_count = $pageGroupModel->total_count($query_details);
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
     *     description="page group json input <br> Sample JSON to create page group http://jsoneditoronline.org/?id=f63e8d7425c66d5b8abf4bcf71eebb46",
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
     *     description="page group json input <br> Sample JSON to update page group http://jsoneditoronline.org/?id=006e448f74cf6f8673d10d8513b2a247",
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
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }

        $page_group_id = "";
        if ($request->isMethod('put')) {
            if (isset($page_data_array['_id']) AND $page_data_array['_id'] != "") {
                $page_group_id = $page_data_array['_id'];
            } else {
                // error page group id is required
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_page_group_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_page_group_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
        }


        $page_group_model = new PageGroupModel();

        if ($page_group_id == "") {
            $page_group_id = $page_group_model->create_page_group();
            $page_data_array['_id'] = $page_group_id;
        }


        $req_json = json_encode($page_data_array);

        $pdf_helper = new Pdf_helper();

        $response_array = array();
        $response_array['page_group_id'] = $page_group_id;
        if (isset($page_data_array['page_group']['page'])) {

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

                        $page_keyword_array = array();
                        if (isset($page['keywords']) AND is_array($page['keywords'])) {
                            $page_keyword_array = $page['keywords'];
                        }
                        foreach ($page_section_array as $section) {

                            $questions_ids = array();
                            $section_question_array = $section['question'];

                            foreach ($section_question_array as $question) {

                                if(isset($question['question_no'])){
                                    $question_number = $question['question_no'];
                                }else{
                                    $question_number = "";
                                }
                                if(isset( $question['question_text'])){
                                    $question_text =  $question['question_text'];
                                }else{
                                    $question_text = "";
                                }
                                
                                if(isset( $question['question_type'])){
                                    $question_type =  $question['question_type'];
                                }else{
                                    $question_type = "";
                                }
                                
                                if(isset( $question['answer_cols'])){
                                    $answer_col =  $question['answer_cols'];
                                }else{
                                    $answer_col = "";
                                }
                                
                                if(isset( $question['image'])){
                                    $question_image_url =  $question['image'];
                                }else{
                                    $question_image_url = "";
                                }
                                
                                if(isset( $question['answer'])){
                                    $answer_array =  $question['answer'];
                                }else{
                                    $answer_array = "";
                                }
                                
                                if(isset($question['mc'])){
                                    $mc_data = $question['mc'];
                                }else{
                                    $mc_data = array();
                                }
                                
                                if(isset($question['optBox'])){
                                    $opt_box_data = $question['optBox'];
                                }else{
                                    $opt_box_data = array();
                                }
                                
                                $insert_data = array(
                                    'question_no' => $question_number,
                                    'answer_cols' => $answer_col,
                                    'question_text' => $question_text,
                                    'image' => $question_image_url,
                                    'answer' => $answer_array,
                                    'question_type' => $question_type,
                                    'x' => $question['x'],
                                    'y' => $question['y'],
                                    'mc' => $mc_data,
                                    'optBox' => $opt_box_data
                                );

                                $question_id = "";
                                if (isset($question['question_id']) AND $question['question_id'] != "") {
                                    $question_id = $question['question_id'];
                                }

                                $question_id = $this->create_question($insert_data, $question_id);

                                $question_keywords = array();
                                if (isset($question['keywords']) AND is_array($question['keywords'])) {
                                    $question_keywords = $question['keywords'];
                                }

                                array_merge($page_keyword_array, $question_keywords);
                                // TODO index each keyword with question_id
                                if (count($question_keywords) > 0) {
                                    foreach ($question_keywords as $keyword) {
                                        // check keyword in DB
                                        $result = KeywordHelper::indexKeyword($keyword, $question_id, config('collection_constants.QUESTION'));
                                    }
                                }
                                array_push($questions_ids, $question_id);
                            }


                            if(isset($section['instruction_text'])){
                                $section_instruction_text = $section['instruction_text'];
                            }elseif(isset($section['instruction']['text'])){
                                $section_instruction_text = $section['instruction']['text'];
                            }else{
                                $section_instruction_text = "";
                            }
                            if(isset($section['section_type'])){
                                $section_type = $section['section_type'];
                            }else{
                                $section_type = "";
                            }
                            if(isset($section['start_question_no'])){
                                $section_start_question_no = $section['start_question_no'];
                            }else{
                                $section_start_question_no = "";
                            }
                            
                            if(isset($section['with_sample_question'])){
                                $section_with_sample_question = $section['with_sample_question'];
                            }else{
                                $section_with_sample_question = "";
                            }
                            
                            if(isset($section['answer_cols'])){
                                $section_answer_cols = $section['answer_cols'];
                            }else{
                                $section_answer_cols = "";
                            }
                            
                            if(isset($section['suggestion_box'])){
                                $section_suggestion_box = $section['suggestion_box'];
                            }else{
                                $section_suggestion_box = "";
                            }
                            
                            if(isset($section['paraBox'])){
                                $section_parabox_data = $section['paraBox'];
                            }else{
                                $section_parabox_data = array();
                            }
                            
                            
                            
                            $section_question = $questions_ids;

                            $insert_data = array(
                                'instruction_text' => $section_instruction_text,
                                'section_type' => $section_type,
                                'start_question_no' => $section_start_question_no,
                                'with_sample_question' => $section_with_sample_question,
                                'answer_cols' => $section_answer_cols,
                                'suggestion_box' => $section_suggestion_box,
                                'paraBox' => $section_parabox_data,
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
                    // page keywords index logic
                    if (isset($page_keyword_array) AND count($page_keyword_array) > 0) {
                        foreach ($page_keyword_array as $keyword) {
                            // check keyword in DB
                            $result = KeywordHelper::indexKeyword($keyword, $page_id, config('collection_constants.PAGE'));
                        }
                    }
                    array_push($page_ids, $page_id);
                }

                $page_group_title = "";
                if (isset($page_data_array['page_group']['title'])) {
                    $page_group_title = $page_data_array['page_group']['title'];
                }

                $page_group_sub_title = "";
                if (isset($page_data_array['page_group']['sub_title'])) {
                    $page_group_sub_title = $page_data_array['page_group']['sub_title'];
                }

                $teachersCopyResponse = $pdf_helper->generate_pdf_from_json($req_json, TRUE);
                $teachersCopyArray = json_decode($teachersCopyResponse, true);
                
                $page_group_insert_data = array(
                    'page' => $page_ids,
                    'title' => $page_group_title,
                    'sub_title' => $page_group_sub_title,
                    'preview_url' => $page_data_array['preview_url'],
                    'preview_image_array' => $page_data_array['preview_image_array'],
                    'layout' => $page_data_array['layout'],
                    'syllabus' => $page_data_array['syllabus'],
                    'student_copy_preview_url' => $page_data_array['preview_url'],
                    'student_preview_image_array' => $page_data_array['preview_image_array'],
                    'teacher_copy_preview_url' => $teachersCopyArray['preview_url'],
                    'teacher_preview_image_array' => $teachersCopyArray['preview_image_array'],
                );
                
                if ($request->isMethod('post')) {
                    $page_group_insert_data['created_by']= Token_helper::fetch_user_id_from_token($request->header('token'));;
                }

                $pageGroup_result = $page_group_model->update_page_group($page_group_insert_data, $page_group_id);
            } else {
                
            }

            // Keyword Logic 
            $keyword_array = array();
            if (isset($page_data_array['page_group']['keywords'])) {
                $keyword_array = $page_data_array['page_group']['keywords'];
            }
            if (count($keyword_array) > 0) {
                foreach ($keyword_array as $keyword) {
                    // check keyword in DB
                    $result = KeywordHelper::indexKeyword($keyword, $page_group_id, config('collection_constants.PAGE_GROUP'));
                }
            }

            if (isset($page_data_array['preview_url'])) {
                $response_array['student_copy_preview_url'] = $page_data_array['preview_url'];
            }
            if (isset($page_data_array['preview_image_array'])) {
                $response_array['student_preview_image_array'] = $page_data_array['preview_image_array'];
            }
            if (isset($page_data_array['preview_image_array'])) {
                $response_array['teacher_copy_preview_url'] = $teachersCopyArray['preview_url'];
            }
            if (isset($page_data_array['preview_image_array'])) {
                $response_array['teacher_preview_image_array'] = $teachersCopyArray['preview_image_array'];
            }
            $response_array['success'] = TRUE;
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
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
        $page_group_data = $pageGroupModel->find_page_group_details($page_group_id);
        if ($page_group_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_page_group_id')['error_code'],
                    "ERR_MSG" => config('error_constants.invalid_page_group_id')['error_message']));

            $response_array = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
        }
        $gcs_result = TRUE;
        if (isset($page_group_data['preview_url']) && $page_group_data['preview_url'] != "") {
            $data = explode("/", $page_group_data['preview_url']); // fetching file name from URL
            $objectName = end($data);
            $gcs_result = GCS_helper::delete_from_gcs($objectName);
        }
        if ($gcs_result) {
            PageGroupModel::destroy($page_group_id);

            $response_array = array("success" => TRUE);
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        } else {
            $error['error'] = array("success" => FALSE, "error" => "Something went wrong");
            return response(json_encode($error), 400)->header('Content-Type', 'application/json');
        }
    }

}
