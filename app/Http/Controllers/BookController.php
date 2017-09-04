<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Token_helper;
use App\BookModel;
use App\DomainModel;
use App\SubDomainModel;
use App\KnowledgeUnitModel;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Pdf_helper;

class BookController extends Controller {
    
        /**
     * @SWG\Get(path="/book",
     *   tags={"Book"},
     *   summary="Returns list of books",
     *   description="Returns book data",
     *   operationId="book_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search parameter or key word to search",
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
     * @SWG\Get(path="/book/{_id}",
     *   tags={"Book"},
     *   summary="Returns book data",
     *   description="Returns book data",
     *   operationId="book_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the book that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid book id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function book_list(Request $request) {
        $bookModel = new BookModel();
        if (isset($request->_id) && $request->_id != "") {
            $book_id = $request->_id;
            $book_details = $bookModel->find_book_details($book_id);
            if ($book_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_book_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_book_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }else{
                $response_array = array("success" => TRUE, "data" => $book_details, "errors" => array());
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

            $book_details = $bookModel->book_details($query_details);
            $total_count = $bookModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $book_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }
    
    /**
     * @SWG\Post(path="/book",
     *   tags={"Book"},
     *   summary="Creates a book",
     *   description="",
     *   operationId="create_book",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="book json input",
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
     * @SWG\Put(path="/book",
     *   tags={"Book"},
     *   summary="Update book details",
     *   description="",
     *   operationId="create_book",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="book json input",
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
    public function create_book(Request $request) {

        $json_data = $request->getContent();

        // $book_raw_json_data = file_get_contents(url('book_json_data.json'));

        $book_data_array = json_decode($json_data, true);

        if ($book_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }

        $book_array = array(
            '_id' => $book_data_array['_id'],
            'page' => $book_data_array['page'],
            'toc' => $book_data_array['toc'],
            'cover' => $book_data_array['cover'],
            'syllabus' => $book_data_array['syllabus'],
            'keyword' => $book_data_array['keyword'],
            'organisation' => $book_data_array['organisation']
        );

        $rules = array(
            'page' => 'required',
            'toc' => 'required',
            'cover' => 'required',
            'syllabus' => 'required',
            'keyword' => 'required',
            'organisation' => 'required'
        );

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

            $rules['_id'] = 'required|exists:book,_id';
        }



        $messages = [
            '_id.required' => config('error_constants.book_id_required'),
            'page.required' => config('error_constants.book_page_required'),
            'toc.required' => config('error_constants.book_toc_required'),
            'cover.required' => config('error_constants.book_cover_required'),
            'syllabus.required' => config('error_constants.book_syllabus_required'),
            'keyword.required' => config('error_constants.book_keyword_required'),
            'organisation.required' => config('error_constants.book_organisation_required')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($book_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));

            $domain = array();
            $sub_domain = array();
            $knowledge_unit = array();

            $domainModel = new DomainModel();
            $subDomainModel = new SubDomainModel();
            $knowledgeUnitModel = new KnowledgeUnitModel();


            foreach ($book_data_array['syllabus']['domain'] as $var_domain) {

                // Check for null and create new ID
                if (isset($var_domain['_id']) && $var_domain['_id'] == "") {

                    // data to create new Knowledge UNIT
                    $data = array(
                        'code' => $var_domain['code'],
                        'title' => $var_domain['title']
                    );

                    $domain_id = $domainModel->create_domain($data);
                } else {
                    $domain_id = $var_domain['_id'];
                }

                $domain_insert_data = array(
                    '_id' => $domain_id,
                    'code' => $var_domain['code'],
                    'title' => $var_domain['title']
                );
                array_push($domain, $domain_insert_data);
            }

            foreach ($book_data_array['syllabus']['subdomain'] as $subdomain) {

                // Check for null and create new ID
                if (isset($subdomain['_id']) && $subdomain['_id'] == "") {

                    // data to create new Knowledge UNIT
                    $data = array(
                        'code' => $subdomain['code'],
                        'title' => $subdomain['title']
                    );
                    $sub_domain_id = $subDomainModel->create_sub_domain($data);
                } else {
                    $sub_domain_id = $subdomain['_id'];
                }

                $subdomain_insert_data = array(
                    '_id' => $sub_domain_id,
                    'code' => $subdomain['code'],
                    'title' => $subdomain['title']
                );

                array_push($sub_domain, $subdomain_insert_data);
            }

            foreach ($book_data_array['syllabus']['knowledge_unit'] as $knowledgeUnit) {

                // Check for null and create new ID
                if (isset($knowledgeUnit['_id']) && $knowledgeUnit['_id'] == "") {

                    // data to create new Knowledge UNIT
                    $data = array(
                        'code' => $knowledgeUnit['code'],
                        'title' => $knowledgeUnit['title']
                    );
                    $knowledgeUnit_id = $knowledgeUnitModel->create_knowledge_unit($data);
                } else {
                    $knowledgeUnit_id = $knowledgeUnit['_id'];
                }

                $knowledgeUnit_insert_data = array(
                    '_id' => $knowledgeUnit_id,
                    'code' => $knowledgeUnit['code'],
                    'title' => $knowledgeUnit['title']
                );

                array_push($knowledge_unit, $knowledgeUnit_insert_data);
            }

            $book_data_array['syllabus']['domain'] = $domain;
            $book_data_array['syllabus']['subdomain'] = $sub_domain;
            $book_data_array['syllabus']['knowledge_unit'] = $knowledge_unit;

            if ($book_data_array['created_by'] == "") {
                $book_data_array['created_by'] = $user_id;
            }


            $data_array = array(
                'page' => $book_data_array['page'],
                'toc' => $book_data_array['toc'],
                'cover' => $book_data_array['cover'],
                'syllabus' => $book_data_array['syllabus'],
                'organisation' => $book_data_array['organisation'],
                'created_by' => $book_data_array['created_by'],
                'updated_by' => $user_id
            );


            $book_id = $book_data_array['_id'];
            $bookModel = new BookModel();
            $result = $bookModel->create_book($data_array, $book_id);

            if ($book_id != "") {
                $success_msg = 'Book Updated Successfully';
            } else {
                $success_msg = 'Book Created Successfully';
            }
            $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }
    
    
    /**
     * @SWG\Delete(path="/book",
     *   tags={"Book"},
     *   summary="delete book data",
     *   description="Delete book from system",
     *   operationId="delete_book",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the book that needs to be deleted",
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
    function delete_book(Request $request) {
        $book_id = trim($request->_id);
        $bookModel = new BookModel();
        $book_data = $bookModel->book_details(array('_id' => $book_id));
        if ($book_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_book_id'),
                                        "ERR_MSG"=> config('error_messages'.".".
                                                            config('error_constants.invalid_book_id')))) ; 
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        BookModel::destroy($book_id);
        $responseArray = array("success" => TRUE, "data" => "Book deleted successfully");
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

    public function print_book(Request $request) {
        $temp_array = array();
        Pdf_helper::generate_book(json_encode($temp_array));
    }

}
