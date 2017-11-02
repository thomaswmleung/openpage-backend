<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrganizationModel;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller {
    /**
     * @SWG\Get(path="/organization",
     * tags={"Organization"},
     *   summary="Returns list of organizations",
     *   description="Returns organization  data",
     *   operationId="organization",
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
     *   )
     * )
     */

    /**
     * @SWG\Get(path="/organization/{_id}",
     * tags={"Organization"},
     *   summary="Returns organization data",
     *   description="Returns organization data",
     *   operationId="organization",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the organization that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid question type id",
     *   )
     * )
     */
    public function organization(Request $request) {
        $organizationModel = new OrganizationModel();
        if (isset($request->_id)) {
            $organization_id = $request->_id;
            $organization_details = $organizationModel->find_organization_details($organization_id);
            if ($organization_details == NULL) {
                $error['error'] = array("Invalid user id");

                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_organization_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_organization_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $organization_details, "errors" => array());
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

            $organization_details = $organizationModel->organization_details($query_details);
            $total_count = $organizationModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $organization_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/organization",
     *   tags={"Organization"},
     *   summary="Creating/Storing new organization",
     *   description="Creation of organization",
     *   operationId="create_organization",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="Name of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="address",
     *     in="query",
     *     description="address of the organization",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     description="email of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="contact_person",
     *     in="query",
     *     description="contact person of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id[]",
     *     in="query",
     *     description="The user ids of organization",
     *     required=true,
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
     *   ),
     *   @SWG\Parameter(
     *     name="logo",
     *     in="query",
     *     description="logo of the organization",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="remark",
     *     in="query",
     *     description="remark of the organization",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="consultant[]",
     *     in="query",
     *     description="The consultants of organization",
     *     required=true,
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
     *   ),
     *   @SWG\Parameter(
     *     name="role[]",
     *     in="query",
     *     description="The roles of organization",
     *     required=true,
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
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
    /**
     * @SWG\Put(path="/organization",
     *   tags={"Organization"},
     *   summary="Updating organization information",
     *   description="Updation of organization",
     *   operationId="create_organization",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="Name of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="address",
     *     in="query",
     *     description="address of the organization",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     description="email of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="contact_person",
     *     in="query",
     *     description="contact person of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of the organization",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id[]",
     *     in="query",
     *     description="The user ids of organization",
     *     required=true,
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
     *   ),
     *   @SWG\Parameter(
     *     name="logo",
     *     in="query",
     *     description="logo of the organization",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="remark",
     *     in="query",
     *     description="remark of the organization",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="consultant[]",
     *     in="query",
     *     description="The consultants of organization",
     *     required=true,
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
     *   ),
     *   @SWG\Parameter(
     *     name="role[]",
     *     in="query",
     *     description="The roles of organization",
     *     required=true,
     *     type="array",
     *      @SWG\Items(
     *             type="string"
     *         ),
     *      collectionFormat="multi",
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
    public function create_organization(Request $request) {
        $organization_array = array(
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
            'contact_person' => $request->contact_person,
            'type' => $request->type,
            'user_id' => $request->user_id,
            'logo' => $request->logo,
            'remark' => $request->remark,
            'consultant' => $request->consultant,
            'role' => $request->role,
        );
        $rules = array(
            'name' => 'required',
            'email' => 'required',
            'contact_person' => 'required',
            'type' => 'required',
            'remark' => 'required',
            'consultant' => 'required',
            'role' => 'required',
        );

        $organization_id = "";
        if ($request->isMethod('put')) {
            $organization_array['_id'] = $request->_id;
            $organization_id = $request->_id;
            $rules['_id'] = 'required|exists:organization';
        }

        $messages = [
            '_id.required' => config('error_constants.organization_id_required'),
            '_id.exists' => config('error_constants.invalid_organization_id'),
            'name.required' => config('error_constants.organization_name_required'),
            'email.required' => config('error_constants.organization_email_required'),
            'contact_person.required' => config('error_constants.organization_contact_person_required'),
            'type.required' => config('error_constants.organization_type_required'),
            'remark.required' => config('error_constants.organization_remark_required'),
            'consultant.required' => config('error_constants.organization_consultant_required'),
            'role.required' => config('error_constants.organization_role_required')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($organization_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            $organizationModel = new OrganizationModel();
            $data = $organizationModel->create_or_update_organization($organization_array, $organization_id);
            $response_array = array("success" => TRUE, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }

    /**
     * @SWG\Delete(path="/organization",
     *   tags={"Organization"},
     *   summary="delete organization data",
     *   description="Delete organization in the system",
     *   operationId="delete_organization",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the organization that needs to be deleted",
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
    public function delete_organization(Request $request) {

        $organization_id = trim($request->_id);
        $organization_array = array(
            '_id' => $organization_id,
        );
        $rules = array(
            '_id' => 'required|exists:organization,_id',
        );
        $messages = [
            '_id.required' => config('error_constants.organization_id_required'),
            '_id.exists' => config('error_constants.organization_doesnot_exist'),
        ];
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);
        $validator = Validator::make($organization_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            OrganizationModel::destroy($organization_id);
            $response_array = array("success" => FALSE);
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }

}
