<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Token_helper;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use App\LayoutModel;

class LayoutController extends Controller {

        public function layout_list(Request $request) {
        $layoutModel = new LayoutModel();
        if (isset($request->_id) && $request->_id != "") {
            $layout_id = $request->_id;

            // get media details
            $data_array = array(
                '_id' => $layout_id
            );
            $layout_details = $layoutModel->layout_details($data_array);
            if ($layout_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_layout_id'),
                        "ERR_MSG" => config('error_messages' . "." .
                                config('error_constants.invalid_layout_id'))));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400);
            }
        } else {
            $layout_details = $layoutModel->layout_details();
        }

        $response_array = array("success" => TRUE, "data" => $layout_details, "errors" => array());
        return response(json_encode($response_array), 200);
    }
    
    
    public function add_or_update_layout(Request $request) {
        $json_data = $request->getContent();
        $layout_data_array = json_decode($json_data, true);

        if ($layout_data_array == null) {
            return response(json_encode(array("error" => "Invalid Json")));
        }

        $layout_array = array(
            '_id' => $layout_data_array['_id'],
            'layout_code' => $layout_data_array['layout_code'],
            'overlay' => $layout_data_array['overlay'],
            'main' => $layout_data_array['main'],
            'background' => $layout_data_array['background'],
            'front_cover' => $layout_data_array['front_cover'],
            'table_of_content' => $layout_data_array['table_of_content'],
            'back_cover' => $layout_data_array['back_cover'],
        );
        // validation : TODO
        
        $layout_id = $layout_data_array['_id'];
        $layoutModel = new LayoutModel();
        $layoutModel->create_or_update_layout($layout_array, $layout_id);
        if ($layout_id != "") {
            $success_msg = 'Layout Updated Successfully';
        } else {
            $success_msg = 'Layout Created Successfully';
        }
        $response_array = array("success" => TRUE, "data" => $success_msg, "errors" => array());
        return response(json_encode($response_array), 200);
    }

     function delete_layout(Request $request) {
        $layout_id = trim($request->_id);
        $layoutModel = new LayoutModel();
        $layout_data = $layoutModel->layout_details(array('_id' => $layout_id));
        if ($layout_data == null) {
            $error['error'] = array("Layout data not found");
            return response(json_encode($error), 400);
        }
        LayoutModel::destroy($layout_id);
        return response("Layout deleted successfully", 200);
    }
}
