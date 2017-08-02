<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\MediaModel;
use App\Helpers\GCS_helper;
use Illuminate\Support\Facades\Config;
use App\Helpers\Token_helper;

class MediaController extends Controller {

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
                return response(json_encode($error), 400);
            }
        } else {
            $media_details = $mediaModel->media_details();
        }

        return response(json_encode($media_details), 200);
    }

    public function create_media(Request $request) {
//        $media_json = $request->getContent();
//        $media_array = json_decode($media_json, TRUE);
        
        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));
        
       
        
        $media_array = array(
            'type'=>$request->type,
            'extension'=>$request->extension,
            'media_file'=>$request->file('media_file'),
            'owner'=>$request->owner,
            'usage'=>$request->usage,
            'created_by'=>$user_id,
        );
        $rules = array(
            'type' => 'required',
            'extension' => 'required',
            'media_file' => 'required|mimes:jpeg,png,jpg,mp3,ogg,mp4,pdf,zip',
            'owner' => 'required',
            'usage' => 'required',
            'created_by' => 'required|exists:users,_id',
        );
        $validator = Validator::make($media_array, $rules);
        if ($validator->fails()) {
            return response($validator->messages(), 400);
        } else {
           
            if ($request->hasFile('media_file')) {
                $image = $request->file('media_file');
                $input['media_file'] = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images');
                $media_name = $input['media_file'];
                $image->move($destinationPath, $media_name);

                //upload to GCS

                $gcs_result = GCS_helper::upload_to_gcs('images/' . $media_name);
                if (!$gcs_result) {
                    $error['error'] = array("Error in upload of GCS");
                    return response(json_encode($error), 400);
                }
                // delete your local pdf file here
                unlink($destinationPath . "/" . $media_name);

                $media_url = "https://storage.googleapis.com/" . Config::get('constants.gcs_bucket_name') . "/" . $media_name;
                $media_array['url'] = $media_url;
            } else {
                $error['error'] = array("Something went wrong");
                return response(json_encode($error), 400);
            }
            //insert media
       
            MediaModel::create($media_array);
            return response("Media created successfully", 200);
        }
    }

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

        $media_array['_id'] = $request->_id;
        if (isset($request->owner) && $request->owner != "") {
            $media_array['owner'] = $request->owner;
        }

        if (isset($request->right) && $request->right != "") {
            $media_array['right'] = $request->right;
        }

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

    public function delete_media(Request $request) {

        $media_id = trim($request->mid);
        $mediaModel = new MediaModel();
        $media_data = $mediaModel->media_details(array('_id' => $media_id));
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
