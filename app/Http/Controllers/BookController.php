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

class BookController extends Controller {

    public function create_book(Request $request) {

        $json_data = $request->getContent();

        // $book_raw_json_data = file_get_contents(url('book_json_data.json'));

        $book_data_array = json_decode($json_data, true);

        if ($book_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")));
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
            
                $rules['_id'] = 'required';
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
//            Log::error("errors in create media");
//            Log::error(json_encode($validator->messages()));
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400);
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
                'syllabus' => $book_data_array['syllabus'],
                'organisation' => $book_data_array['organisation'],
                'created_by' => $book_data_array['created_by'],
                'updated_by' => $user_id
            );

            // echo "<pre>";        print_r($data_array);exit;

            $book_id = $book_data_array['_id'];
            $bookModel = new BookModel();
            $result = $bookModel->create_book($data_array, $book_id);

            // echo $result;

            if ($result != "") {
                if ($result == $book_id) {
                    $success_msg = 'Book Updated Successfully';
                } else {
                    $success_msg = 'Book Created Successfully';
                }
                $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
                return response(json_encode($response_array), 200);
            } else {
                echo "Something went Wrong";
            }
        }
    }

}
