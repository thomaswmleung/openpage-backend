<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PageModel;
use App\MainModel;
use App\SectionModel;
use App\QuestionsModel;
class PageController extends Controller {
    /**
     * @SWG\Get(path="/page",
     *   tags={"Page"},
     *   summary="Returns list of page",
     *   description="Returns page data",
     *   operationId="page_list",
     *   produces={"application/json"},
     *   parameters={},
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
     * @SWG\Get(path="/page/{page_id}",
     *   tags={"Page"},
     *   summary="Returns page data",
     *   description="Returns page data",
     *   operationId="section_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="page_id",
     *     in="path",
     *     description="ID of the page that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid page id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function page_list(Request $request) {
        $page_id = NULL;
        if (isset($request->page_id) && $request->page_id != "") {
            $page_id = $request->page_id;
            $pageModel = new PageModel();
            $exists = $pageModel->get_page_details($page_id);
            if (!$exists) {
                $response_array = array("success" => FALSE, "errors" => "Invalid page id");
                return response(json_encode($response_array), 400);
            }
        }

//        $skip = NULL;
        $skip = 0;
        if (isset($request->skip) && $request->skip != "") {
            $skip = $request->skip;
        }
//        $limit = NULL;
        $limit = 100;
        if (isset($request->limit) && $request->limit != "") {
            $limit = $request->limit;
        }

        $data_array = array(
            '_id' => $page_id,
            'skip' => $skip,
            'limit' => $limit
        );

        $pageModel = new PageModel();
        $page_details = $pageModel->page_list($data_array);

        $response_array = array("success" => TRUE, "data" => $page_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }
    
    /**
     * @SWG\Get(path="/page_search",
     *   tags={"Page"},
     *   summary="Returns page data based on search keyword",
     *   description="Returns page data",
     *   operationId="page_search",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search keyword that needs to searched in pages",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid page id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function page_search(Request $request) {
        $search_key = $request->search_key;
//      $skip = NULL;
        $skip = 0;
        if (isset($request->skip) && $request->skip != "") {
            $skip = $request->skip;
        }
//      $limit = NULL;
        $limit = 100;
        if (isset($request->limit) && $request->limit != "") {
            $limit = $request->limit;
        }
        $data_array = array(
            'search_key' => $search_key,
            'skip' => $skip,
            'limit' => $limit
        );
        $pageModel = new PageModel();
        $page_details = $pageModel->page_search($data_array);
        
        $response_array = array("success" => TRUE, "data" => $page_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }

    /**
     * @SWG\Post(path="/page",
     *   tags={"Page"},
     *   summary="Create a page",
     *   description="",
     *   operationId="add_or_update_page",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="page json input",
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
     * @SWG\Put(path="/page",
     *   tags={"Page"},
     *   summary="Update section details",
     *   description="",
     *   operationId="add_or_update_page",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="page json input",
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
    public function add_or_update_page(Request $request) {
        $json_data = $request->getContent();
        $page = json_decode($json_data, true);
        if ($page == null) {
            return response(json_encode(array("error" => "Invalid Json")));
        }

//        $pdf_helper = new Pdf_helper();
//        $pdf_page_response_json = $pdf_helper->generate_page_pdf_from_json($json_data);

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

                $question_id = $this->create_or_update_question($insert_data, $question_id);


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

            $section_id = $this->create_or_update_section($insert_data, $section_id);


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

        if (isset($page['page_id']) && $page['page_id'] != "") {
            // get page_id from main collection
            $page_id = $page['page_id'];
            $pageModel = new PageModel();
            $main_id = $pageModel->fetch_main_id($page_id);
        }

        $main_id = $this->create_or_update_main($insert_main_data, $main_id);


        $ovelay_data = $page['overlay'];
        /*  $page_ovelay_id = $this->create_overlay($ovelay_data); */

        $back_ground_data = $page['background'];
        /*  $page_back_ground_id = $this->create_background($back_ground_data); */

        $page_remark = $page['remark'];

        $insert_page_data = array(
            'overlay' => $ovelay_data,
            'main_id' => $main_id,
            'background' => $back_ground_data,
            'remark' => $page_remark
        );

        $page_id = "";
        if (isset($page['page_id']) && $page['page_id'] != "") {

            $page_id = $page['page_id'];
        }

        $page_id = $this->create_or_update_page($insert_page_data, $page_id);

        $response_array['success'] = TRUE;

        return response(json_encode($response_array), 200);
    }

    function create_or_update_question($insert_data, $question_id) {
        $questionModel = new QuestionsModel();
        $questionDetails = $questionModel->add_questions($insert_data, $question_id);
        return $questionDetails->_id;
    }

    function create_or_update_section($insert_data, $section_id) {
        $sectionModel = new SectionModel();
        $sectionDetails = $sectionModel->add_section($insert_data, $section_id);
        return $sectionDetails->_id;
    }

    function create_or_update_main($insert_data, $main_id_) {
        $mainModel = new MainModel();
        $model_details = $mainModel->add_main($insert_data, $main_id_);
        return $model_details->_id;
    }

    function create_or_update_page($insert_page_data, $page_id) {
        $pageModel = new PageModel();
        $page_result = $pageModel->add_or_update_page($insert_page_data, $page_id);
        return $page_result->_id;
    }
    
    /**
     * @SWG\Delete(path="/page",
     *   tags={"Page"},
     *   summary="delete page data",
     *   description="Delete page from system",
     *   operationId="delete_page",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="pid",
     *     in="query",
     *     description="ID of the page that needs to be deleted",
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
    function delete_page(Request $request) {
        $page_id = trim($request->pid);
        $pageModel = new PageModel();
        $page_data = $pageModel->get_page(array('_id' => $page_id));
        if ($page_data == null) {
            $error['error'] = array("page not found");
            return response(json_encode($error), 400);
        }
        PageModel::destroy($page_id);
        return response("Page deleted successfully", 200);
    }

}
