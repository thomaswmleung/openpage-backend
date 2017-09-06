<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Pdf_helper;
use Illuminate\Support\Facades\Log;
use App\SubjectModel;
use App\ParticularModel;
use App\KnowledgeUnitModel;
use App\SubDomainModel;
use App\DomainModel;
use App\Helpers\ErrorMessageHelper;

class SubjectController extends Controller {
    /**
     * @SWG\Get(path="/subject",
     *   tags={"Subject"},
     *   summary="Returns list of subjects",
     *   description="Returns subject data",
     *   operationId="subject_list",
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
     * @SWG\Get(path="/subject/{_id}",
     *   tags={"Subject"},
     *   summary="Returns subject data",
     *   description="Returns subject data",
     *   operationId="subject_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the subject that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid subject id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function subject_list(Request $request) {
        $subjectModel = new SubjectModel();
        if (isset($request->_id) && $request->_id != "") {
            $subject_id = $request->_id;
            $subject_details = $subjectModel->find_subject_details($subject_id);
            if ($subject_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_subject_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_subject_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }else{
                $response_array = array("success" => TRUE, "data" => $subject_details, "errors" => array());
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

            $subject_details = $subjectModel->subject_details($query_details);
            $total_count = $subjectModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $subject_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/subject",
     *   tags={"Subject"},
     *   summary="Create a Subject",
     *   description="",
     *   operationId="create_subject",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="subject json input",
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
     * @SWG\Put(path="/subject",
     *   tags={"Subject"},
     *   summary="Update Subject details",
     *   description="",
     *   operationId="create_subject",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Subject json input",
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
    public function create_subject(Request $request) {

        $json_data = $request->getContent();

        // $subject_raw_json_data = file_get_contents(url('subject_json_data.json'));

        $subject_data_array = json_decode($json_data, true);

        if ($subject_data_array == null) {
            return response(json_encode(array("success"=>FALSE,"error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }

        $subject_array = array(
            '_id' => $subject_data_array['_id'],
            'code' => $subject_data_array['code'],
            'title' => $subject_data_array['title'],
            'domain' => $subject_data_array['domain']
        );
        $rules = array(
            'code' => 'required',
            'title' => 'required',
            'domain' => 'required'
        );

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

            $rules['_id'] = 'required';
        }

        $messages = [
            '_id.required' => config('error_constants.subject_id_required'),
            'code.required' => config('error_constants.subject_code_required'),
            'title.required' => config('error_constants.subject_title_required'),
            'domain.required' => config('error_constants.subject_domain_required')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($subject_array, $rules, $formulated_messages);
        if ($validator->fails()) {
//            Log::error("errors in create media");
//            Log::error(json_encode($validator->messages()));
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400);
        } else {



            $subject_id = $subject_data_array['_id'];

//        if (isset($subject_data_array['_id']) && $subject_data_array['_id'] == "") {
//            $subject_id = $subject_model->create_subject();
//            $subject_data_array['_id'] = $subject_id;
//        }


            $response_array = array();
            $response_array['subject_id'] = $subject_id;

            $subject_domain_array = $subject_data_array['domain'];
//        $subject_subdomain_array = $subject_data_array['domain']['subdomain'];
//        $subject_knowledge_unit_array = $subject_data_array['domain']['subdomain']['knowledge_unit'];
//        $subject_particular_array = $subject_data_array['domain']['subdomain']['knowledge_unit']['particular'];
//        

            $domain = array();
            $subdomain = array();
            $knowledge_unit = array();
            $particular = array();


            $domainModel = new DomainModel();
            $subDomainModel = new SubDomainModel();
            $particularModel = new ParticularModel();
            $knowledgeUnitModel = new KnowledgeUnitModel();
            $subjectModel = new SubjectModel();


            foreach ($subject_domain_array as $var_domain) {

                $subject_subdomain_array = $var_domain['subdomain'];

                foreach ($subject_subdomain_array as $var_subdomain) {

                    $subject_knowledge_unit_array = $var_subdomain['knowledge_unit'];


                    foreach ($subject_knowledge_unit_array as $var_knowledge_unit) {

                        $subject_particular_array = $var_knowledge_unit['particular'];

                        foreach ($subject_particular_array as $var_particular) {

                            // Check for null and create new ID
                            if (isset($var_particular['_id']) && $var_particular['_id'] == "") {

                                // data to create new Particular
                                $data = array(
                                    'detail' => $var_particular['detail'],
                                    'title' => $var_particular['title']
                                );

                                $particular_id = $particularModel->create_particular($data);
                            } else {
                                $particular_id = $var_particular['_id'];
                            }

                            $particular_data = array(
                                '_id' => $particular_id,
                                'detail' => $var_particular['detail'],
                                'title' => $var_particular['title']
                            );

                            array_push($particular, $particular_data);
                        }

                        // Check for null and create new ID
                        if (isset($var_knowledge_unit['_id']) && $var_knowledge_unit['_id'] == "") {

                            // data to create new Knowledge UNIT
                            $data = array(
                                'code' => $var_knowledge_unit['code'],
                                'title' => $var_knowledge_unit['title']
                            );

                            $knowledge_unit_id = $knowledgeUnitModel->create_knowledge_unit($data);
                        } else {
                            $knowledge_unit_id = $var_knowledge_unit['_id'];
                        }

                        $knowledge_data = array(
                            '_id' => $knowledge_unit_id,
                            'code' => $var_knowledge_unit['code'],
                            'title' => $var_knowledge_unit['title'],
                            'subdomain_predecessor' => $var_knowledge_unit['subdomain_predecessor'],
                            'ku_predecessor' => $var_knowledge_unit['ku_predecessor'],
                            'particular' => $particular
                        );

                        array_push($knowledge_unit, $knowledge_data);
                    }

                    // Check for null and create new ID
                    if (isset($var_subdomain['_id']) && $var_subdomain['_id'] == "") {

                        // data to create new sub Domain
                        $data = array(
                            'code' => $var_knowledge_unit['code'],
                            'title' => $var_knowledge_unit['title']
                        );

                        $sub_domain_id = $subDomainModel->create_sub_domain($data);
                    } else {
                        $sub_domain_id = $var_subdomain['_id'];
                    }

                    $subdomain_data = array(
                        '_id' => $sub_domain_id,
                        'code' => $var_subdomain['code'],
                        'title' => $var_subdomain['title'],
                        'domain_predecessor' => $var_subdomain['domain_predecessor'],
                        'subdomain_predecessor' => $var_subdomain['subdomain_predecessor'],
                        'knowledge_unit' => $knowledge_unit,
                    );

                    array_push($subdomain, $subdomain_data);
                }

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

                $domain_data = array(
                    '_id' => $domain_id,
                    'code' => $var_domain['code'],
                    'title' => $var_domain['title'],
                    'domain_predecessor' => $var_domain['domain_predecessor'],
                    'subdomain' => $subdomain
                );

                array_push($domain, $domain_data);
            }

            $subject_data = array(
                'code' => $subject_data_array['code'],
                'title' => $subject_data_array['title'],
                'domain' => $domain
            );

            $result = $subjectModel->create_subject($subject_data, $subject_id);


            if (count($result)) {
                $response_array['success'] = TRUE;
                return response('Subject ' . ($subject_id == "" ? " Created " : " Updated ") . ' Successfully', 200)->header('Content-Type', 'application/json');
            }
        }
    }

    /**
     * @SWG\Delete(path="/subject",
     *   tags={"Subject"},
     *   summary="delete subject data",
     *   description="Delete subject from system",
     *   operationId="delete_subject",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the subject that needs to be deleted",
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
    function delete_subject(Request $request) {
        $subject_id = trim($request->_id);
        $subjectModel = new SubjectModel();
        $subject_data = $subjectModel->subject_details(array('_id' => $subject_id));
        if ($subject_data == null) {
            $error['error'] = array("subject not found");
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_subject_id')['error_code'],
                    "ERR_MSG" => config('error_constants.invalid_subject_id')['error_message']));

            $response_array = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
        }
        SubjectModel::destroy($subject_id);
        $response_array = array("success" => TRUE);
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

}
