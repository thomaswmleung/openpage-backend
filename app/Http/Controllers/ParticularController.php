<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\ParticularModel;

class ParticularController extends Controller {
    /**
     * @SWG\Get(path="/particular",
     *   tags={"Particular"},
     *   summary="Returns list of Particular ",
     *   description="Returns Particular  data",
     *   operationId="particular_list",
     *   produces={"application/json"},
     *   parameters={},
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
     * @SWG\Get(path="/particular/{_id}",
     *   tags={"Particular"},
     *   summary="Returns Particular  flow data",
     *   description="Returns Particular  data",
     *   operationId="particular_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the Particular  that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid Particular  id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function particular_list(Request $request) {
        $particularModel = new ParticularModel();

        if (isset($request->_id) && $request->_id != "") {

            $particular_id = $request->_id;
            $particular_details = $particularModel->find_particular_details($particular_id);
            if ($particular_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.particular_id_invalid')['error_code'],
                        "ERR_MSG" => config('error_constants.particular_id_invalid')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $particular_details, "errors" => array());
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

            $particular_details = $particularModel->particular_details($query_details);
            $total_count = $particularModel->total_count($search_key);
        }

        $response_array = array("success" => TRUE, "data" => $particular_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/particular",
     *   tags={"Particular"},
     *   summary="Create a particular",
     *   description="",
     *   operationId="add_or_update_particular",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Create particular document <br> Sample JSON http://jsoneditoronline.org/?id=b2f6cc5c7648377b4367795a45dd0fc8",
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
     * @SWG\Put(path="/particular",
     *   tags={"Particular"},
     *   summary="Update particular details",
     *   description="",
     *   operationId="add_or_update_particular",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="Update particular document <br> Sample JSON http://jsoneditoronline.org/?id=5f498ec51cd3cb139241dfdd61a181c3",
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
    public function add_or_update_particular(Request $request) {
        $json_data = $request->getContent();
        $particular_data_array = json_decode($json_data, true);

        if ($particular_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }
        $particular_id = "";
        if (isset($particular_data_array['_id'])) {
            $particular_id = $particular_data_array['_id'];
        }
        $title = "";
        if (isset($particular_data_array['title'])) {
            $title = $particular_data_array['title'];
        }
        $detail = "";
        if (isset($particular_data_array['detail'])) {
            $detail = $particular_data_array['detail'];
        }
        

        $particular_array = array(
            '_id' => $particular_id,
            'title' => $title,
            'detail' => $detail
        );

        // validation : TODO
        if ($request->isMethod('post')) {
            $rules = array(
                'title' => 'required',
                'detail' => 'required'
            );
            $messages = [
                'detail.required' => config('error_constants.particular_detail_required'),
                'title.required' => config('error_constants.particular_title_required'),
            ];
        }
        if ($request->isMethod('put')) {
            $rules = array(
                '_id' => 'required|exists:particular',
                'title' => 'required',
                'detail' => 'required',
            );
            $messages = [
                '_id.required' => config('error_constants.particular_id_invalid'),
                '_id.exists' => config('error_constants.particular_id_invalid'),
                'detail.required' => config('error_constants.particular_detail_required'),
                'title.required' => config('error_constants.particular_title_required'),
            ];
        }
        
        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($particular_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }


        $particularModel = new ParticularModel();
        $particularModel->create_or_update_particular($particular_array, $particular_id);
        if ($particular_id != "") {
            $success_msg = 'Particular Updated Successfully';
        } else {
            $success_msg = 'Particular Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Delete(path="/particular",
     *   tags={"Particular"},
     *   summary="delete particular data",
     *   description="Delete knowledge  from system",
     *   operationId="delete_particular",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the particular that needs to be deleted",
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
    function delete_particular(Request $request) {
        $particular_id = trim($request->_id);
        $particularModel = new ParticularModel();
        $particular_data = $particularModel->find_particular_details($particular_id);
        if ($particular_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.particular_id_invalid')['error_code'],
                    "ERR_MSG" => config('error_constants.particular_id_invalid')['error_message']));
            $responseArray = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        }
        ParticularModel::destroy($particular_id);
        $responseArray = array("success" => TRUE);
        return response(json_encode($responseArray), 200)->header('Content-Type', 'application/json');
    }

}
