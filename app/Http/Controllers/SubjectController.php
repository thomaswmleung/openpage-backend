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


/*
 *  Class Name : PageGroupController
 *  Description : This controller handles parsing of JSON DATA and save to DB
 * 
 * 
 */

class SubjectController extends Controller {

    public function create_subject(Request $request) {

        // dd('v');
        $json_data = $request->getContent();
        
       // $subject_raw_json_data = file_get_contents(url('subject_json_data.json'));

        $subject_data_array = json_decode($json_data, true);
        // $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));
        // echo "<pre>"; print_r($subject_data_array);exit;
        


        if ($subject_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")));
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
                return response('Subject ' . ($subject_id == "" ? " Created " : " Updated ") . ' Successfully', 200);
            }
        }
    }

}
