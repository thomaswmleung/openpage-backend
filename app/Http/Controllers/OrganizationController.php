<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrganizationModel;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller {
    /**
     * @SWG\Get(path="/organization",
     * tags={"organization"},
     *   summary="Returns list of organizations",
     *   description="Returns organization  data",
     *   operationId="organization",
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
     * @SWG\Get(path="/organization/{_id}",
     * tags={"organization"},
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
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function organization(Request $request) {
        $organizationModel = new OrganizationModel();
        if (isset($request->_id)) {


            $organization_id = $request->_id;
            // get user details
            $data_array = array(
                '_id' => $organization_id
            );
            $organization_details = $organizationModel->organization_details($data_array);
            if ($organization_details == NULL) {
                $error['error'] = array("Invalid user id");

                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_organization_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_organization_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
        } else {

            
            $organization_details = $organizationModel->organization_details();
        }

        $response_array = array("success" => TRUE, "data" => $organization_details, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/organization",
     *   tags={"organization"},
     *   summary="Creating/Storing new organization",
     *   description="Creation of organization",
     *   operationId="create_organization",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="name of the question",
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

        $messages = [
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
            OrganizationModel::create($organization_array);
            $response_array = array("success" => TRUE, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }
    
    /**
     * @SWG\Delete(path="/organization",
     *   tags={"organization"},
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
