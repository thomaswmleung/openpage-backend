<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CodexModel;
use App\Helpers\GCS_helper;
use Illuminate\Support\Facades\Config;
use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\Helpers\ErrorMessageHelper;

class CodexController extends Controller {
    /**
     * @SWG\Get(path="/codex",
     *   tags={"Codex"},
     *   summary="Returns list of codex",
     *   description="Returns codex data",
     *   operationId="codex",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search based on name, description, label",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     description="Filter by user id",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="label",
     *     in="query",
     *     description="Filter by label ",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="from_date",
     *     in="query",
     *     description="Create at start date(YYYY-mm-dd) ",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="to_date",
     *     in="query",
     *     description="Create at end date(YYYY-mm-dd) ",
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
     *             enum={"created_at", "name", "description","label"},
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
     * @SWG\Get(path="/codex/{cid}",
     *   tags={"Codex"},
     *   summary="Returns codex data",
     *   description="Returns codex data",
     *   operationId="codex",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="cid",
     *     in="path",
     *     description="ID of the codex that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid codex id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function codex(Request $request) {
        $codexModel = new CodexModel();
//        dd(($request->cid);
        if (isset($request->cid) && $request->cid != "") {
            $codex_id = $request->cid;
            $codex_details = $codexModel->find_codex_details($codex_id);
            
            if ($codex_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_codex_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_codex_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $codex_details, "errors" => array());
                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
            }
        } else {
            $search_key = "";
            if (isset($request->search_key)) {
                $search_key = $request->search_key;
            }
            $user_id = "";
            if (isset($request->user_id)) {
                $user_id = $request->user_id;
            }
            $name = "";
            if (isset($request->name)) {
                $name = $request->name;
            }
            $description = "";
            if (isset($request->description)) {
                $description = $request->description;
            }
            $label = "";
            if (isset($request->label)) {
                $label = $request->label;
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
                'user_id' => $user_id,
                'name' => $name,
                'description' => $description,
                'label' => $label,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'limit' => $limit,
                'skip' => $skip,
                'sort_by' => $sort_by,
                'order_by' => $order_by,
            );
            
            $codex_details = $codexModel->codex_details($query_details);
          
            $total_count = $codexModel->total_count($query_details);
        }

        $response_array = array("success" => TRUE, "data" => $codex_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/codex",
     *   tags={"Codex"},
     *   consumes={"multipart/form-data"},
     *   summary="Creating/Storing new codex",
     *   description="Stores codex in the system",
     *   operationId="create_codex",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="Name of the codex",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="query",
     *     description="description of the codex",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *         description="code image to upload",
     *         in="formData",
     *         name="codex_image",
     *         required=true,
     *         type="file"
     *     ),
     *   @SWG\Parameter(
     *     name="label",
     *     in="query",
     *     description="label of the codex",
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
    public function create_codex(Request $request) {
        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));

        $codex_array = array(
            'name' => $request->name,
            'description' => $request->description,
            'codex_image' => $request->file('codex_image'),
            'label' => $request->label,
            'created_by' => $user_id,
        );
        $rules = array(
            'name' => 'required',
            'codex_image' => 'max:10240|mimes:jpeg,bmp,png,jpg,gif,tiff',
            'created_by' => 'required|exists:users,_id',
        );

        $messages = [
            'name.required' => config('error_constants.codex_name_required'),
            'codex_image.required' => config('error_constants.codex_file_required'),
            'codex_image.max' => config('error_constants.codex_file_limit_exceeded'),
            'codex_image.mimes' => config('error_constants.invalid_codex_file_mime'),
            'created_by.required' => config('error_constants.codex_created_by_required'),
            'created_by.exists' => config('error_constants.invalid_codex_created_by')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($codex_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {

            if ($request->hasFile('codex_image')) {
                $image = $request->file('codex_image');
                $input['codex_image'] = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images');
                $codex_name = $input['codex_image'];
                $image->move($destinationPath, $codex_name);

                //upload to GCS

                $gcs_result = GCS_helper::upload_to_gcs('images/' . $codex_name);
                if (!$gcs_result) {
                    $error['error'] = array("success" => FALSE, "error" => "Error in upload of GCS");
                    return response(json_encode($error), 400)->header('Content-Type', 'application/json');
                }
                // delete your local pdf file here
                unlink($destinationPath . "/" . $codex_name);

                $codex_url = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $codex_name;
                $codex_array['codex_image'] = $codex_url;
            } else {
                $response_array = array("success" => FALSE, "errors" => "Something went wrong");
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
            //insert codex

            $codex_data = CodexModel::create($codex_array);
            $response_array = array("success" => TRUE, "data" => $codex_data, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }

    /**
     * @SWG\Put(path="/codex",
     *   tags={"Codex"},
     *   summary="Update codex  data",
     *   description="Update codex  in the system",
     *   operationId="update_codex",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="cid",
     *     in="query",
     *     description="ID of the codex that needs to be updated",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="Name of the codex",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="query",
     *     description="description of the codex",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="label",
     *     in="query",
     *     description="label of the codex",
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
    public function update_codex(Request $request) {

        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));

        $rules = array(
            '_id' => 'required|exists:codex,_id',
        );


        $codex_array = array();

        $codex_array['_id'] = $request->cid;

        if (isset($request->name) && $request->name != "") {
            $codex_array['name'] = $request->name;
        }
        if (isset($request->description) && $request->description != "") {
            $codex_array['description'] = $request->description;
        }
        if (isset($request->label) && $request->label != "") {
            $codex_array['label'] = $request->label;
        }


        $codex_array['updated_by'] = $user_id;
        $messages = [
            'name.required' => config('error_constants.codex_name_required'),
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($codex_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {
            //$codex_array = $request->all();

            $result = $this->update_codex_data($codex_array);
            if ($result) {
                $responseArray = array("success" => TRUE, "data" => "Codex updated successfully");
                return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
            } else {
                $responseArray = array("success" => FALSE, "error" => "Something went wrong");
                return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
            }
        }
    }

    public function update_codex_data($data) {
        $codexModel = new CodexModel();
        return $codexModel->update_codex($data);
    }

    /**
     * @SWG\Delete(path="/codex",
     *   tags={"Codex"},
     *   summary="delete codex  data",
     *   description="Delete codex file in the system",
     *   operationId="delete_codex",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="cid",
     *     in="query",
     *     description="ID of the codex that needs to be deleted",
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
    public function delete_codex(Request $request) {

        $codex_id = trim($request->cid);

        $codexModel = new CodexModel();
        $codex_data = $codexModel->find_codex_details($codex_id);
        if ($codex_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_codex_id')['error_code'],
                    "ERR_MSG" => config('error_constants.invalid_codex_id')['error_message']));

            $response_array = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
        }
        $data = explode("/", $codex_data['codex_image']); // fetching file name from URL
        $objectName = end($data);
        $gcs_result = GCS_helper::delete_from_gcs($objectName);
        if ($gcs_result) {
            CodexModel::destroy($codex_id);
            $response_array = array("success" => TRUE);
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        } else {
            $responseArray = array("success" => FALSE, "errors" => array(array('ERROR_CODE' => "GLOBAL_ERROR",
                        'ERR_MSG' => 'Something went wrong.')));
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
    }

}
