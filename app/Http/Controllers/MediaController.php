<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\MediaModel;
use App\Helpers\GCS_helper;
use Illuminate\Support\Facades\Config;
use App\Helpers\Token_helper;
use Illuminate\Support\Facades\Log;
use App\Helpers\ErrorMessageHelper;


class MediaController extends Controller {
    /**
     * @SWG\Get(path="/media",
     *   tags={"Media"},
     *   summary="Returns list of media",
     *   description="Returns media data",
     *   operationId="media",
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
     * @SWG\Get(path="/media/{mid}",
     *   tags={"Media"},
     *   summary="Returns media data",
     *   description="Returns media data",
     *   operationId="media",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="mid",
     *     in="path",
     *     description="ID of the media that needs to be displayed",
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
    public function media(Request $request) {
        // die(var_dump($request->header('token')));
       
        $mediaModel = new MediaModel();
        if (isset($request->mid) && $request->mid != "") {
            $media_id = $request->mid;

            // get media details
            $data_array = array(
                '_id' => $media_id
            );
            $media_details = $mediaModel->media_details($data_array);
            if ($media_details == NULL) {
                $error['error'] = array("Invalid media id");
                 $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_media_id'),
                                        "ERR_MSG"=> config('error_messages'.".".
                                                            config('error_constants.invalid_media_id')))) ; 
                
                $response_array = array("success"=>FALSE,"errors"=>$error_messages);
                return response(json_encode($response_array), 400);
            }
        }else {
            $media_details = $mediaModel->media_details();
        }
        
        $response_array = array("success" => TRUE,"data"=>$media_details,"errors"=>array());
        return response(json_encode($response_array), 200);
    }

    /**
     * @SWG\Post(path="/media",
     *   tags={"Media"},
     *   consumes={"multipart/form-data"},
     *   summary="Creating/Storing new media file",
     *   description="Stores media file in the system",
     *   operationId="create_media",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of media file to be uploaded",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="extension",
     *     in="query",
     *     description="Extension of media file",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *         description="file to upload",
     *         in="formData",
     *         name="media_file",
     *         required=true,
     *         type="file"
     *     ),
     *   @SWG\Parameter(
     *     name="owner",
     *     in="query",
     *     description="user_id or organization_id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="usage[]",
     *     in="query",
     *     description="The page ids array field.",
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
    public function create_media(Request $request) {
        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));
        $media_array = array(
            'type' => $request->type,
            'extension' => $request->extension,
            'media_file' => $request->file('media_file'),
            'owner' => $request->owner,
            'usage' => $request->usage,
            'created_by' => $user_id,
        );
        $rules = array(
            'type' => 'required',
            'extension' => 'required',
            'media_file' => 'required|mimes:jpeg,png,jpg,mp3,ogg,mp4,pdf,zip',
            'owner' => 'required',
            'usage' => 'required',
            'created_by' => 'required|exists:users,_id',
        );
        
        $messages = [
            'type.required' => config('error_constants.media_type_required'),
            'extension.required' => config('error_constants.media_extension_required'),
            'media_file.required' => config('error_constants.media_file_required'),
            'media_file.mimes' => config('error_constants.invalid_media_file_mime'),
            'owner.required' => config('error_constants.media_owner_required'),
            'usage.required' => config('error_constants.media_usage_required'),
            'created_by.required' => config('error_constants.media_created_by_required'),
            'created_by.exists' => config('error_constants.invalid_media_created_by')
        ];
        
         $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);
         
        $validator = Validator::make($media_array, $rules,$formulated_messages);
        if ($validator->fails()) {
//            Log::error("errors in create media");
//            Log::error(json_encode($validator->messages()));
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400);
        } else {

            if ($request->hasFile('media_file')) {
                $image = $request->file('media_file');
                $input['media_file'] = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images');
                $media_name = $input['media_file'];
                $image->move($destinationPath, $media_name);

                //upload to GCS

//                $gcs_result = GCS_helper::upload_to_gcs('images/' . $media_name);
//                if (!$gcs_result) {
//                    $error['error'] = array("Error in upload of GCS");
//                    return response(json_encode($error), 400);
//                }
//                // delete your local pdf file here
//                unlink($destinationPath . "/" . $media_name);
//
//                $media_url = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $media_name;
//                $media_array['url'] = $media_url;
                $media_array['url'] = "oiuytr";
                
            } else {
                $error['error'] = array("Something went wrong");
                return response(json_encode($error), 400);
            }
            //insert media

            MediaModel::create($media_array);
            $response_array = array("success" => TRUE,"errors"=>array());
//            Log::error(json_encode($response_array));
            return response(json_encode($response_array), 200);
        }
    }

    /**
     * @SWG\Put(path="/media",
     *   tags={"Media"},
     *   summary="Update media file data",
     *   description="Update media file in the system",
     *   operationId="update_media",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="mid",
     *     in="query",
     *     description="ID of the media that needs to be updated",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="owner",
     *     in="query",
     *     description="user_id or organization_id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="usage[]",
     *     in="query",
     *     description="The page ids array field.",
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
    public function update_media(Request $request) {
        
//        $media_json = $request->getContent();
//        $media_array = json_decode($media_json, TRUE);
//        dd($request->json()->all());
//
        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));
        
        $rules = array(
            '_id' => 'required|exists:media,_id',
                // 'updated_by' => 'required|exists:users,_id',
        );

        
        $media_array = array();

        $media_array['_id'] = $request->mid;
        if (isset($request->owner) && $request->owner != "") {
            $media_array['owner'] = $request->owner;
        }
        \Illuminate\Support\Facades\Log::error(json_encode($request->mid));
        if (isset($request->usage) && $request->usage != "") {
            $media_array['usage'] = $request->usage;
        }


        $media_array['updated_by'] = $user_id;

   
        $validator = Validator::make($media_array, $rules);
        if ($validator->fails()) {
            return response($validator->messages(), 400);
        } else {
            //$media_array = $request->all();

            $result = $this->update_media_data($media_array);
            if ($result) {
                return response("Media updated successfully", 200);
            } else {
                $error['error'] = array("Something went wrong");
                return response(json_encode($error), 400);
            }
        }
    }

    public function update_media_data($data) {
        $mediaModel = new MediaModel();
        return $mediaModel->update_media($data);
    }

    /**
     * @SWG\Delete(path="/media",
     *   tags={"Media"},
     *   summary="delete media file data",
     *   description="Delete media file in the system",
     *   operationId="delete_media",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="mid",
     *     in="query",
     *     description="ID of the media that needs to be deleted",
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
    public function delete_media(Request $request) {

        $media_id = trim($request->mid);

        $mediaModel = new MediaModel();
        $media_data = $mediaModel->media_details(array('_id' => $media_id));
        if ($media_data == null) {
            $error['error'] = array("media not found");
            return response(json_encode($error), 400);
        }
        $data = explode("/", $media_data['url']); // fetching file name from URL
        $objectName = end($data);
        $gcs_result = GCS_helper::delete_from_gcs($objectName);
        if ($gcs_result) {
            MediaModel::destroy($media_id);
            return response("Media Deleted successfully", 200);
        } else {
            $error['error'] = array("Something went wrong");
            return response(json_encode($error), 400);
        }
    }

}
