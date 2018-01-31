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
use Imagick;
use ImagickPixel;
use DNS2D;

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
     *     name="codex",
     *     in="query",
     *     description="Filter by codex",
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
     *     name="subject",
     *     in="query",
     *     description="Filter by subject",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="domain",
     *     in="query",
     *     description="Filter by domain",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="subdomain",
     *     in="query",
     *     description="Filter by subdomain",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="learning_objective",
     *     in="query",
     *     description="Filter by learning objective",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="particulars",
     *     in="query",
     *     description="Filter by particulars",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="level_of_difficulty",
     *     in="query",
     *     description="Filter by level of difficulty",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="syllabus_code",
     *     in="query",
     *     description="Filter by syllabus code",
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
            $codex = "";
            if (isset($request->codex)) {
                $codex = $request->codex;
            }
            $sub_title = "";
            if (isset($request->sub_title)) {
                $sub_title = $request->sub_title;
            }
            $subject = "";
            if (isset($request->subject)) {
                $subject = $request->subject;
            }
            $domain = "";
            if (isset($request->domain)) {
                $domain = $request->domain;
            }
            $subdomain = "";
            if (isset($request->subdomain)) {
                $subdomain = $request->subdomain;
            }
            $level_of_difficulty = "";
            if (isset($request->level_of_difficulty)) {
                if (is_numeric($request->level_of_difficulty)) {
                    $level_of_difficulty = (int) $request->level_of_difficulty;
                }
            }
            $particulars = "";
            if (isset($request->particulars)) {
                $particulars = $request->particulars;
            }
            $learning_objective = "";
            if (isset($request->learning_objective)) {
                $learning_objective = $request->learning_objective;
            }
            $syllabus_code = "";
            if (isset($request->syllabus_code)) {
                $syllabus_code = $request->syllabus_code;
            }
            $created_by = "";
            if (isset($request->created_by)) {
                $created_by = $request->created_by;
            }
            $from_date = "";
            if (isset($request->from_date)) {
                $from_date = date("Y-m-d H:i:s", strtotime($request->from_date . "00:00:00"));
            }
            $to_date = "";
            if (isset($request->to_date)) {
                $to_date = date("Y-m-d H:i:s", strtotime($request->to_date . " 23:59:59"));
            }

            if ($from_date == "" || $to_date == "") {
                $from_date = "";
                $to_date = "";
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
                'codex' => $codex,
                'sub_title' => $sub_title,
                'subject' => $subject,
                'domain' => $domain,
                'subdomain' => $subdomain,
                'level_of_difficulty' => $level_of_difficulty,
                'particulars' => $particulars,
                'learning_objective' => $learning_objective,
                'syllabus_code' => $syllabus_code,
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
//        echo $json_data;
//        exit();
        $page_data_array = json_decode($json_data, true);

//        var_dump($page_data_array);
//        exit();
        if ($page_data_array == null) {
            Log::error("json could not be converted to array");
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }

        $page_group_id = "";
        if ($request->isMethod('put')) {
            if (isset($page_data_array['_id']) AND $page_data_array['_id'] != "") {
                $page_group_id = $page_data_array['_id'];

//                var_dump($page_group_id);
//                exit();
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
        if (isset($page_data_array['page_group']['page']) || isset($page_data_array['page_group']['import_url'])) {

            $pdf_response_json = $pdf_helper->generate_pdf_from_json($req_json);
            $page_data_array = json_decode($pdf_response_json, true);

//            dd($page_data_array);

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

                                if (isset($question['question_no'])) {
                                    $question_number = $question['question_no'];
                                } else {
                                    $question_number = "";
                                }
                                if (isset($question['question_text'])) {
                                    $question_text = $question['question_text'];
                                } else if (isset($question['text'])) {
                                    $question_text = $question['text'];
                                } else {
                                    $question_text = "";
                                }

                                if (isset($question['question_type'])) {
                                    $question_type = $question['question_type'];
                                } else {
                                    $question_type = "";
                                }

                                if (isset($question['answer_cols'])) {
                                    $answer_col = $question['answer_cols'];
                                } else {
                                    $answer_col = "";
                                }

                                if (isset($question['image'])) {
                                    $question_image_url = $question['image'];
                                } else {
                                    $question_image_url = "";
                                }

                                if (isset($question['answer'])) {
                                    $answer_array = $question['answer'];
                                } else {
                                    $answer_array = "";
                                }

                                if (isset($question['mc'])) {
                                    $mc_data = $question['mc'];
                                } else {
                                    $mc_data = array();
                                }

                                if (isset($question['optBox'])) {
                                    $opt_box_data = $question['optBox'];
                                } else {
                                    $opt_box_data = array();
                                }
                                if (!isset($question['x'])) {
                                    $question['x'] = "";
                                }
                                if (!isset($question['y'])) {
                                    $question['y'] = "";
                                }

                                if (!isset($question['cols'])) {
                                    $question['cols'] = "";
                                }

                                if (isset($question['mc'])) {
                                    $mc_data = $question['mc'];
                                } else {
                                    $mc_data = array();
                                }





                                $insert_data = array(
                                    'question_no' => $question_number,
                                    'answer_cols' => $answer_col,
                                    'text' => $question_text,
                                    'cols' => $question['cols'],
                                    'mc' => $mc_data,
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


                            if (isset($section['instruction_text'])) {
                                $section_instruction_text = $section['instruction_text'];
                            } elseif (isset($section['instruction']['text'])) {
                                $section_instruction_text = $section['instruction']['text'];
                            } else {
                                $section_instruction_text = "";
                            }
                            if (isset($section['type'])) {
                                $section_type = $section['type'];
                            } else {
                                $section_type = "";
                            }
                            if (isset($section['start_question_no'])) {
                                $section_start_question_no = $section['start_question_no'];
                            } else {
                                $section_start_question_no = "";
                            }

                            if (isset($section['with_sample_question'])) {
                                $section_with_sample_question = $section['with_sample_question'];
                            } else {
                                $section_with_sample_question = "";
                            }

                            if (isset($section['answer_cols'])) {
                                $section_answer_cols = $section['answer_cols'];
                            } else {
                                $section_answer_cols = "";
                            }

                            if (isset($section['suggestion_box'])) {
                                $section_suggestion_box = $section['suggestion_box'];
                            } else {
                                $section_suggestion_box = "";
                            }

                            if (isset($section['paraBox'])) {
                                $section_parabox_data = $section['paraBox'];
                            } else {
                                $section_parabox_data = array();
                            }

                            if (isset($section['instruction'])) {
                                $instruction_data = $section['instruction'];
                            } else {
                                $instruction_data = array();
                            }

                            if (isset($section['paraBox'])) {
                                $parabox_data = $section['paraBox'];
                            } else {
                                $parabox_data = array();
                            }

                            if (isset($section['optBox'])) {
                                $optbox_data = $section['optBox'];
                            } else {
                                $optbox_data = array();
                            }



                            $section_question = $questions_ids;

                            $insert_data = array(
                                'instruction_text' => $section_instruction_text,
                                'instruction' => $instruction_data,
                                'paraBox' => $parabox_data,
                                'optBox' => $optbox_data,
                                'section_type' => $section_type,
                                'start_question_no' => $section_start_question_no,
                                'with_sample_question' => $section_with_sample_question,
                                'answer_cols' => $section_answer_cols,
                                'suggestion_box' => $section_suggestion_box,
                                'paraBox' => $section_parabox_data,
                                'question' => $section_question
                            );

                            if (isset($section['table'])) {
                                $insert_data['table'] = $section['table'];
                            }

                            if (isset($section['table_data'])) {
                                $insert_data['table_data'] = $section['table_data'];
                            }

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
            }
        }

        $original_request_array = json_decode($req_json, TRUE);
        if (isset($original_request_array['page_group']['page']) || isset($original_request_array['page_group']['teachers_import_url'])) {
            $teachersCopyResponse = $pdf_helper->generate_pdf_from_json($req_json, TRUE);
            $teachersCopyArray = json_decode($teachersCopyResponse, true);
        } else {
            $teachersCopyArray['preview_url'] = "";
            $teachersCopyArray['preview_image_array'] = array();
        }

        $page_group_insert_data['version_number'] = 1;
        if (isset($page_data_array['page_group']['version_number']) AND $page_data_array['page_group']['version_number'] !=NULL AND $page_data_array['page_group']['version_number'] !="") {
            $page_group_insert_data['version_number'] = $page_data_array['page_group']['version_number'];
        }
        if (isset($page_data_array['page_group']['title'])) {
            $page_group_insert_data['title'] = $page_data_array['page_group']['title'];
        }
        if (isset($page_data_array['page_group']['sub_title'])) {
            $page_group_insert_data['sub_title'] = $page_data_array['page_group']['sub_title'];
        }
        if (isset($page_data_array['page_group']['subject'])) {
            $page_group_insert_data['subject'] = $page_data_array['page_group']['subject'];
        }
        if (isset($page_data_array['page_group']['domain'])) {
            $page_group_insert_data['domain'] = $page_data_array['page_group']['domain'];
        }
        if (isset($page_data_array['page_group']['subdomain'])) {
            $page_group_insert_data['subdomain'] = $page_data_array['page_group']['subdomain'];
        }
        if (isset($page_data_array['page_group']['codex'])) {
            $page_group_insert_data['codex'] = $page_data_array['page_group']['codex'];
        }
        if (isset($page_data_array['page_group']['area'])) {
            $page_group_insert_data['area'] = $page_data_array['page_group']['area'];
        }
        if (isset($page_data_array['page_group']['author'])) {
            $page_group_insert_data['author'] = $page_data_array['page_group']['author'];
        }
        if (isset($page_data_array['page_group']['remark'])) {
            $page_group_insert_data['remark'] = $page_data_array['page_group']['remark'];
        }
        if (isset($page_data_array['page_group']['level_of_difficulty'])) {
            $page_group_insert_data['level_of_difficulty'] = $page_data_array['page_group']['level_of_difficulty'];
        }
        if (isset($page_data_array['page_group']['learning_objective'])) {
            $page_group_insert_data['learning_objective'] = $page_data_array['page_group']['learning_objective'];
        }
        if (isset($page_data_array['page_group']['particulars'])) {
            $page_group_insert_data['particulars'] = $page_data_array['page_group']['particulars'];
        }
        if (isset($page_data_array['page_group']['syllabus_code'])) {
            $page_group_insert_data['syllabus_code'] = $page_data_array['page_group']['syllabus_code'];
        }
        if (isset($page_data_array['page_group']['level_of_scaffolding'])) {
            $page_group_insert_data['level_of_scaffolding'] = $page_data_array['page_group']['level_of_scaffolding'];
        }
        if (isset($page_data_array['page_group']['metadata'])) {
            $page_group_insert_data['metadata'] = $page_data_array['page_group']['metadata'];
        }
        if (isset($page_data_array['layout'])) {
            $page_group_insert_data['layout'] = $page_data_array['layout'];
        }
        if (isset($page_data_array['syllabus'])) {
            $page_group_insert_data['syllabus'] = $page_data_array['syllabus'];
        }
        if (isset($page_ids)) {
            $page_group_insert_data['page'] = $page_ids;
        }

        if (isset($page_data_array['preview_url'])) {
            $page_group_insert_data['preview_url'] = $page_data_array['preview_url'];
            $page_group_insert_data['student_copy_preview_url'] = $page_data_array['preview_url'];
        }
        if (isset($page_data_array['preview_image_array'])) {
            $page_group_insert_data['preview_image_array'] = $page_data_array['preview_image_array'];
        }

        // teachers realated 
        if (isset($teachersCopyArray['preview_url'])) {
            $page_group_insert_data['teacher_copy_preview_url'] = $teachersCopyArray['preview_url'];
        }
        if (isset($teachersCopyArray['preview_image_array'])) {
            $page_group_insert_data['teacher_preview_image_array'] = $teachersCopyArray['preview_image_array'];
        }

        if (isset($page_data_array['version'])) {
            $page_group_insert_data['current_version_details'] = $page_data_array['version'];
        }
        if (isset($page_data_array['affiliation'])) {
            $page_group_insert_data['affiliation'] = $page_data_array['affiliation'];
        }
        if (isset($page_data_array['parent_page_group_id']) AND $page_data_array['parent_page_group_id'] != "" AND $page_data_array['parent_page_group_id'] != NULL) {
            $page_group_insert_data['parent_page_group_id'] = $page_data_array['parent_page_group_id'];
        }

        if ($request->isMethod('post')) {
            $page_group_insert_data['created_by'] = Token_helper::fetch_user_id_from_token($request->header('token'));
        }
        $pageGroup_result = $page_group_model->update_page_group($page_group_insert_data, $page_group_id);

        // Query to check if parent page group id exists
        $current_page_group_data = $page_group_model->find_page_group_details($page_group_id);
        if (isset($current_page_group_data['parent_page_group_id']) AND $current_page_group_data['parent_page_group_id'] != "" AND $current_page_group_data['parent_page_group_id'] != NULL) {
            $parent_page_group_id = $current_page_group_data['parent_page_group_id'];
            $version_array = array();
            $page_group_insert_data['current_version_details']['version_id'] = $page_group_id;
            $page_group_insert_data['current_version_details']['students_preview_image'] = $page_data_array['preview_image_array'][0];
            if (isset($teachersCopyArray['preview_image_array'][0])) {
                $page_group_insert_data['current_version_details']['teachers_preview_image'] = $teachersCopyArray['preview_image_array'][0];
            } else {
                $page_group_insert_data['current_version_details']['teachers_preview_image'] = "";
            }
            if (isset($page_data_array['page_group']['import_url'])) {
                $page_group_insert_data['current_version_details']['import_url'] = $page_data_array['page_group']['import_url'];
            }
            $version_array = $page_group_insert_data['current_version_details'];
            $parent_page_group_data = $page_group_model->find_page_group_details($parent_page_group_id);
            $isFound = FALSE;
            if (isset($parent_page_group_data['versions'])) {
                $versionArray = $parent_page_group_data['versions'];
                $arrayIndex = 0;
                foreach ($versionArray as $value) {
                    if (isset($value['version_id']) AND $value['version_id'] == $page_group_id) {
                        $update_version_result = $page_group_model->update_version_data($parent_page_group_id, $version_array, $arrayIndex);
                        $isFound = TRUE;
                    }
                    $arrayIndex++;
                }
            }
            if (!$isFound) {
                $result = $page_group_model->version_update($parent_page_group_id, $version_array);
            }
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

        if (isset($page_ids) AND sizeof($page_ids) > 0) {
            $response_array['page_id_array'] = $page_ids;
        }

        $response_array['success'] = TRUE;

        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
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
        if (isset($page_group_data['parent_page_group_id']) AND $page_group_data['parent_page_group_id'] != "" AND $page_group_data['parent_page_group_id'] != NULL) {
            $parent_page_group_id = $page_group_data['parent_page_group_id'];
            $parent_page_group_data = $pageGroupModel->find_page_group_details($parent_page_group_id);
            if (isset($parent_page_group_data['versions'])) {
                $versionArray = $parent_page_group_data['versions'];
                $arrayIndex = 0;
                foreach ($versionArray as $value) {
                    if (isset($value['version_id']) AND $value['version_id'] == $page_group_id) {
                        $update_version_result = $pageGroupModel->remove_version_data($parent_page_group_id, $page_group_id);
                    }
                    $arrayIndex++;
                }
            }
            
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

    function imageTest() {
//        $page_index = 0;
////        $pdf_path = public_path("test/test.pdf");
//        $pdf_path = public_path("test/test.pdf");
//        $pdf_img = new Imagick();
//        $pdf_img->setresolution(210, 297);
//        $pdf_img->readimage($pdf_path . "[" . $page_index . "]");
////        $pdf_img->setImageResolution(210,297);
////        $pdf_img->setImageBackgroundColor('white');
////        $pdf_img->paintTransparentImage($pdf_img->getImageBackgroundColor(), 0, 3000);
////        $im->paintTransparentImage("rgb(255,0,255)", 0, 10);
////        $pdf_img->resampleImage  (2100,2970, Imagick::FILTER_UNDEFINED,1);
////        $pdf_img->resizeImage( 2100, 2970, Imagick::FILTER_UNDEFINED, 1, FALSE );
////        $pdf_img->resizeImage( 2100, 2970, Imagick::FILTER_LANCZOS, 1, TRUE );
////        $pdf_img->scaleImage(2100, 2970, false);
//        $pdf_img->scaleImage(1050, 1485);
//        $pdf_img->setImageFormat('jpg');
//        $pdf_img->setImageCompression(imagick::COMPRESSION_JPEG);
//        $pdf_img->setImageCompressionQuality(100);
//
//
//        $pdf_img->setImageCompose(Imagick::COMPOSITE_ATOP);
//        $pdf_img->setImageAlphaChannel(11);
//        $pdf_img->setImageBackgroundColor('white');
//        $pdf_img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
//
//        $pdf_img->writeImage(public_path("test/myImage.jpg"));
//        echo DNS2D::getBarcodeHTML("4445645656", "QRCODE");
//        $path = public_path("tmp/");
//        DNS2D::setStorPath($path);
//        echo DNS2D::getBarcodePNGPath("Surajsa Pawar1", "QRCODE");        
    }

}
