<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPExcel;
use ZipArchive;
use App\Helpers\Token_helper;
use App\BulkUploadRequestModel;
use App\Helpers\ErrorMessageHelper;
use Illuminate\Support\Facades\Validator;
use PHPExcel_IOFactory;
use App\BulkUploadQueueModel;
use Illuminate\Support\Facades\File;
use App\Helpers\GCS_helper;
use Illuminate\Support\Facades\Config;
use App\PageGroupModel;
use Illuminate\Support\Facades\Log;

class BulkPageGroupUploadController extends Controller {

    /**
     * @SWG\Post(path="/bulk_upload",
     *   tags={"Bulk Upload Page Group"},
     *   consumes={"multipart/form-data"},
     *   summary="Bulk upload api for creating page groups",
     *   description="Bulk upload api for creating page groups",
     *   operationId="bulk_upload",
     *   produces={"application/json"},
     *    @SWG\Parameter(
     *         description="Archive file to upload(.zip)",
     *         in="formData",
     *         name="archive_file",
     *         required=true,
     *         type="file"
     *     ),
     *    @SWG\Parameter(
     *         description="Meta data file to upload(.xlsx)",
     *         in="formData",
     *         name="meta_data_csv_file",
     *         required=true,
     *         type="file"
     *     ),
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
    public function bulk_upload(Request $request) {


        if (!file_exists(public_path("bulk_upload_archives"))) {
            mkdir(public_path("bulk_upload_archives"), 0777, true);
        }
        $objPHPExcel = new PHPExcel();
//        $zipDirectory = public_path("bulk_upload_archives/sample.zip");
//        $zip = new ZipArchive();
//        $targetPath = public_path('bulk_upload_archives/target_path1');
//        if ($zip->open($zipDirectory) === TRUE) {
//            $zip->extractTo($targetPath);
//            $zip->close();
//            echo 'ok';
//        } else {
//            echo 'failed';
//        }

        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));

        $media_array = array(
            'archive_file' => $request->file('archive_file'),
            'meta_data_csv_file' => $request->file('meta_data_csv_file'),
            'meta_data_file_extension' => strtolower($request->file('meta_data_csv_file')->getClientOriginalExtension())
        );

        $rules = array(
            'archive_file' => 'required|mimetypes:application/zip',
//            'meta_data_csv_file' => 'required|mimetypes:application/application/application/excel,
//                                        application/vnd.ms-excel, application/vnd.msexcel,vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
            'meta_data_csv_file' => 'required',
            'meta_data_file_extension' => 'required|in:xlsx,xls'
        );

        $messages = [
            'archive_file.required' => config('error_constants.archive_file_required'),
            'archive_file.mimetypes' => config('error_constants.invalid_archive_file'),
            'meta_data_csv_file.required' => config('error_constants.meta_data_file_required'),
            'meta_data_file_extension.in' => config('error_constants.invalid_meta_data_file')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($media_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {

            if ($request->hasFile('archive_file')) {
                $archiveFile = $request->file('archive_file');
                $metaDataFile = $request->file('meta_data_csv_file');
                $metaDataFileName = $metaDataFile->getClientOriginalName();
                // Create bulk upload request in Mongo

                $bulkUploadRequestModel = new BulkUploadRequestModel();
                $author = Token_helper::fetch_user_id_from_token($request->header('token'));
                $archive_file_name = $archiveFile->getClientOriginalName();
                $dataArray = array(
                    'archive_file_name' => $archive_file_name,
                    'created_by' => $author
                );

                $bulkUploadRequestDetails = $bulkUploadRequestModel->create_request($dataArray);



                $zipDirectory = public_path("bulk_upload_archives". DIRECTORY_SEPARATOR.
                        $bulkUploadRequestDetails->_id);
                $zipFilePath = $zipDirectory . "/" . $archive_file_name;

                $metaDataPath = $zipDirectory . "/" . $metaDataFileName;

                $archiveFile->move($zipDirectory, $archive_file_name);
                $metaDataFile->move($zipDirectory, $metaDataFile->getClientOriginalName());
                $zip = new ZipArchive();
                $targetPath = public_path('bulk_upload_archives'. DIRECTORY_SEPARATOR. $bulkUploadRequestDetails->_id);
                if ($zip->open($zipFilePath) === TRUE) {
                    $zip->extractTo($targetPath);
                    $zip->close();
                } else{
                    // Throw error
                    echo "error";
                    exit();
                }

                $file_type = PHPExcel_IOFactory::identify($metaDataPath);
                $objReader = PHPExcel_IOFactory::createReader($file_type);
                $page_list_Excel = $objReader->load($metaDataPath);


                // Read Excel sheet and generate Bulk Upload Request Details

                $page_list_Excel->setActiveSheetIndex(0);
                $sheet = $page_list_Excel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $request_details = array();
                $response_array = array();
                $response_array['request_id'] = $bulkUploadRequestDetails->_id;

                for ($row = 2; $row <= $highestRow; $row++) {

                    $rowData = $sheet->rangeToArray('A' . $row . ':' . 'N' . $row, NULL, TRUE, FALSE);
                    if ($rowData[0][0] == "") {
                        break;
                    }

                    $import_file = $rowData[0][0];
                    $page_group_title = $rowData[0][1];
                    $page_group_sub_title = $rowData[0][2];
                    $page_group_subject = $rowData[0][3];
                    
                    
                    
                    $page_group_codex = $rowData[0][5];
                    $page_group_syllabus_code = $rowData[0][6];
                    $page_group_domain = $rowData[0][7];
                    $page_group_sub_domain = $rowData[0][8];
                    $page_group_area = $rowData[0][9]; 
                    $page_group_knowledge_unit = $rowData[0][10]; 
                    $page_group_learning_objective = $rowData[0][11]; 
                    $page_group_particulars = $rowData[0][12]; 
                    $page_group_level_of_difficulty = $rowData[0][13]; 
                    $page_group_copyright_content = $rowData[0][14]; 
                    $page_group_copyright_artwork = $rowData[0][15]; 
                    $page_group_copyright_photo = $rowData[0][16]; 
                    $page_group_linkage = $rowData[0][17]; 
                    $page_group_user = $rowData[0][18]; 
                    $page_group_level = $rowData[0][19]; 
                    $page_group_nature = $rowData[0][20]; 
                    $page_group_position = $rowData[0][21]; 
                    $page_group_output = $rowData[0][22]; 
                    
                    
//                    $page_group_teacher_copy = $rowData[0][6];
//                    $level_of_difficulty = $rowData[0][7];
//                    $level_of_scaffolding = $rowData[0][8];

                    $queueData = array(
                        'bulk_request_id' => $bulkUploadRequestDetails->_id,
                        'import_file_name' => $import_file,
                        'page_group_title' => $page_group_title,
                        'page_group_subtitle' => $page_group_sub_title,
                        'subject' => $page_group_subject,
                        'syllabus_code' => $page_group_syllabus_code,
                        'codex' => $page_group_codex,
                        'area' => $page_group_area,
                        'domain' => $page_group_domain,
                        'sub_domain' => $page_group_sub_domain,
                        'knowledge_unit' => $page_group_knowledge_unit,
                        'learning_objective' => $page_group_learning_objective,
                        'particulars' => $page_group_particulars,
                        'level_of_difficulty' => $page_group_level_of_difficulty,
                        'copyright_content' => $page_group_copyright_content,
                        'copyright_artwork' => $page_group_copyright_artwork,
                        'copyright_photo' => $page_group_copyright_photo,
                        'linkage' => $page_group_linkage,
                        'user' => $page_group_user,
                        'artwork' => $page_group_artwork,
                        'artwork' => $page_group_artwork,
                        'teacher_copy' => $page_group_teacher_copy,
                        'status' => 'PENDING',
                        'level_of_difficulty' => $level_of_difficulty,
                        'level_of_scaffolding' => $level_of_scaffolding
                    );

                    $bulkUploadQueueModel = new BulkUploadQueueModel();
                    $queueData = $bulkUploadQueueModel->add_to_queue($queueData);
//                    array_push($request_details, $rowData);
                }


                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');

//                    var_dump($request_details);
//                $destinationPath = public_path('images');
//                $media_name = $input['media_file'];
//                $image->move($destinationPath, $media_name);
            } else {

                $response_array['ERROR'] = "Something went wrong.";
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
        }
    }

    
    public function bulk_upload_back_up(Request $request) {


        if (!file_exists(public_path("bulk_upload_archives"))) {
            mkdir(public_path("bulk_upload_archives"), 0777, true);
        }
        $objPHPExcel = new PHPExcel();
//        $zipDirectory = public_path("bulk_upload_archives/sample.zip");
//        $zip = new ZipArchive();
//        $targetPath = public_path('bulk_upload_archives/target_path1');
//        if ($zip->open($zipDirectory) === TRUE) {
//            $zip->extractTo($targetPath);
//            $zip->close();
//            echo 'ok';
//        } else {
//            echo 'failed';
//        }

        $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));

        $media_array = array(
            'archive_file' => $request->file('archive_file'),
            'meta_data_csv_file' => $request->file('meta_data_csv_file'),
            'meta_data_file_extension' => strtolower($request->file('meta_data_csv_file')->getClientOriginalExtension())
        );

        $rules = array(
            'archive_file' => 'required|mimetypes:application/zip',
//            'meta_data_csv_file' => 'required|mimetypes:application/application/application/excel,
//                                        application/vnd.ms-excel, application/vnd.msexcel,vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
            'meta_data_csv_file' => 'required',
            'meta_data_file_extension' => 'required|in:xlsx,xls'
        );

        $messages = [
            'archive_file.required' => config('error_constants.archive_file_required'),
            'archive_file.mimetypes' => config('error_constants.invalid_archive_file'),
            'meta_data_csv_file.required' => config('error_constants.meta_data_file_required'),
            'meta_data_file_extension.in' => config('error_constants.invalid_meta_data_file')
        ];

        $formulated_messages = ErrorMessageHelper::formulateErrorMessages($messages);

        $validator = Validator::make($media_array, $rules, $formulated_messages);
        if ($validator->fails()) {
            $response_error_array = ErrorMessageHelper::getResponseErrorMessages($validator->messages());
            $responseArray = array("success" => FALSE, "errors" => $response_error_array);
            return response(json_encode($responseArray), 400)->header('Content-Type', 'application/json');
        } else {

            if ($request->hasFile('archive_file')) {
                $archiveFile = $request->file('archive_file');
                $metaDataFile = $request->file('meta_data_csv_file');
                $metaDataFileName = $metaDataFile->getClientOriginalName();
                // Create bulk upload request in Mongo

                $bulkUploadRequestModel = new BulkUploadRequestModel();
                $author = Token_helper::fetch_user_id_from_token($request->header('token'));
                $archive_file_name = $archiveFile->getClientOriginalName();
                $dataArray = array(
                    'archive_file_name' => $archive_file_name,
                    'created_by' => $author
                );

                $bulkUploadRequestDetails = $bulkUploadRequestModel->create_request($dataArray);



                $zipDirectory = public_path("bulk_upload_archives". DIRECTORY_SEPARATOR.
                        $bulkUploadRequestDetails->_id);
                $zipFilePath = $zipDirectory . "/" . $archive_file_name;

                $metaDataPath = $zipDirectory . "/" . $metaDataFileName;

                $archiveFile->move($zipDirectory, $archive_file_name);
                $metaDataFile->move($zipDirectory, $metaDataFile->getClientOriginalName());
                $zip = new ZipArchive();
                $targetPath = public_path('bulk_upload_archives'. DIRECTORY_SEPARATOR. $bulkUploadRequestDetails->_id);
                if ($zip->open($zipFilePath) === TRUE) {
                    $zip->extractTo($targetPath);
                    $zip->close();
                } else {
                    // Throw error
                    echo "error";
                    exit();
                }

                $file_type = PHPExcel_IOFactory::identify($metaDataPath);
                $objReader = PHPExcel_IOFactory::createReader($file_type);
                $page_list_Excel = $objReader->load($metaDataPath);


                // Read Excel sheet and generate Bulk Upload Request Details

                $page_list_Excel->setActiveSheetIndex(0);
                $sheet = $page_list_Excel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $request_details = array();
                $response_array = array();
                $response_array['request_id'] = $bulkUploadRequestDetails->_id;

                for ($row = 2; $row <= $highestRow; $row++) {

                    $rowData = $sheet->rangeToArray('A' . $row . ':' . 'N' . $row, NULL, TRUE, FALSE);
                    if ($rowData[0][0] == "") {
                        break;
                    }

                    $import_file = $rowData[0][0];
                    $page_group_title = $rowData[0][1];
                    $page_group_sub_title = $rowData[0][2];
                    $page_group_subject = $rowData[0][3];
                    $page_group_domain = $rowData[0][4];
                    $page_group_sub_domain = $rowData[0][5];
                    $page_group_teacher_copy = $rowData[0][6];
                    $level_of_difficulty = $rowData[0][7];
                    $level_of_scaffolding = $rowData[0][8];

                    $queueData = array(
                        'bulk_request_id' => $bulkUploadRequestDetails->_id,
                        'import_file_name' => $import_file,
                        'page_group_title' => $page_group_title,
                        'page_group_subtitle' => $page_group_sub_title,
                        'subject' => $page_group_subject,
                        'domain' => $page_group_domain,
                        'sub_domain' => $page_group_sub_domain,
                        'teacher_copy' => $page_group_teacher_copy,
                        'status' => 'PENDING',
                        'level_of_difficulty' => $level_of_difficulty,
                        'level_of_scaffolding' => $level_of_scaffolding
                    );

                    $bulkUploadQueueModel = new BulkUploadQueueModel();
                    $queueData = $bulkUploadQueueModel->add_to_queue($queueData);
//                    array_push($request_details, $rowData);
                }


                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');

//                    var_dump($request_details);
//                $destinationPath = public_path('images');
//                $media_name = $input['media_file'];
//                $image->move($destinationPath, $media_name);
            } else {

                $response_array['ERROR'] = "Something went wrong.";
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
        }
    }

    
    public function create_page_group_cron() {
//
//        $file1 = public_path('bulk_upload_archives' . DIRECTORY_SEPARATOR . 'target_path/'.'1.txt');
//        $file2 = public_path('bulk_upload_archives' . DIRECTORY_SEPARATOR . 'target_path1/'.'new1.txt');
//        
//        if (!copy($file1, $file2)) {
//            echo "failed to copy $file...\n";
//        }
//        exit();
        $bulkUploadQueueModel = new BulkUploadQueueModel();
        $queue_entity = $bulkUploadQueueModel->fetch_pending_entity();
        Log::error("Started to pick the queue");
        if ($queue_entity != NULL) {
            
            Log::error("FOUND Item in queue" . $queue_entity->_id);
            $bulkUploadReqId = $queue_entity->bulk_request_id;
            $queue_id = $queue_entity->_id;

            $bulkUploadQueueModel->update_queue_status("IN_PROGRESS", $queue_id);
            // Copy the file to another temp folder before uploading to gcs

            $input_file = public_path('bulk_upload_archives'
                    . DIRECTORY_SEPARATOR . $bulkUploadReqId
                    . DIRECTORY_SEPARATOR . $queue_entity->import_file_name);
            if (file_exists($input_file)) {
                $tmp_folder_path = public_path('bulk_upload_archives'
                        . DIRECTORY_SEPARATOR . $bulkUploadReqId
                        . DIRECTORY_SEPARATOR . $bulkUploadReqId . '_tmp');
                if (!file_exists($tmp_folder_path)) {
                    mkdir($tmp_folder_path, 0777, true);
                }

                $gcs_upload_file_name = $bulkUploadReqId . '_' . $queue_entity->import_file_name;
                $upload_file_path = $tmp_folder_path . DIRECTORY_SEPARATOR . $gcs_upload_file_name;
                if(!copy($input_file, $upload_file_path)){
                    Log::error("Failed to copy the file ===>  " .$upload_file_path );
                }
                
                
                // Upload file to GCS

                $gcs_result = GCS_helper::upload_to_gcs($upload_file_path);

                //upload image to GCS
                if (!$gcs_result) {
                    Log::error("File failed to upload to GCS ==> " . $upload_file_path);
                    $responseArray['error'] = "Error in upload of GCS";
                    exit();
                }
                unlink($upload_file_path);

                $gcs_path = "https://storage.googleapis.com/" .
                        Config::get('constants.gcs_bucket_name') . "/" . $gcs_upload_file_name;
                $bulkUploadRequestModel = new BulkUploadRequestModel();
                $bulkRequestDetails = $bulkUploadRequestModel->fetch_details($bulkUploadReqId);
                if ($bulkRequestDetails != NULL) {
                    $author_id = $bulkRequestDetails->created_by;
                    $tokenHelper = new Token_helper();
                    $user_token = $tokenHelper->generate_user_token($author_id);
                }

                // CURL call to create page_group

                $page_group_data = array("page_group" => array('import_url' => $gcs_path,
                        'title' => $queue_entity->page_group_title,
                        'sub_title' => $queue_entity->page_group_subtitle,
                        'subject' => $queue_entity->subject,
                        'domain' => $queue_entity->domain,
                        'subdomain' => $queue_entity->sub_domain,
                        'page' => array(),
                        'level_of_difficulty' => $queue_entity->level_of_difficulty,
                        'level_of_scaffolding' => $queue_entity->level_of_scaffolding,
                ));
                $ch = curl_init();
                $url = "http://localhost/openpage-backend/public/page_group";
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($page_group_data));  //Post Fields
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $headers = [
                    "token: $user_token"
                ];

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $page_group_response_json = curl_exec($ch);

                curl_close($ch);
                 Log::error("Page Group CURL result ==>" . $page_group_response_json);
                $page_group_details = json_decode($page_group_response_json, TRUE);
                $page_group_id = $page_group_details['page_group_id'];

                // upload teacher copy if exists

                $teacher_copy_file = public_path('bulk_upload_archives'
                        . DIRECTORY_SEPARATOR . $bulkUploadReqId
                        . DIRECTORY_SEPARATOR . $queue_entity->teacher_copy);
                if (file_exists($teacher_copy_file)) {
                    $tmp_folder_path = public_path('bulk_upload_archives'
                            . DIRECTORY_SEPARATOR . $bulkUploadReqId
                            . DIRECTORY_SEPARATOR . $bulkUploadReqId . '_tmp');
                    if (!file_exists($tmp_folder_path)) {
                        mkdir($tmp_folder_path, 0777, true);
                    }

                    $gcs_upload_file_name = $bulkUploadReqId . '_' . $queue_entity->teacher_copy;
                    $upload_file_path = $tmp_folder_path . DIRECTORY_SEPARATOR . $gcs_upload_file_name;
                    copy($teacher_copy_file, $upload_file_path);

                    // Upload file to GCS

                    $gcs_result = GCS_helper::upload_to_gcs($upload_file_path);

                    //upload image to GCS
                    if (!$gcs_result) {
                        $responseArray['error'] = "Error in upload of GCS";
                        Log::error("GCS falied to upload teacher copy for page group id : $page_group_id");
                        exit();
                    }
                    unlink($upload_file_path);

                    $gcs_path = "https://storage.googleapis.com/" .
                            Config::get('constants.gcs_bucket_name') . "/" . $gcs_upload_file_name;


                    $page_group_data = array('teacher_copy_preview_url' => $gcs_path);

                    $pageGroupModel = new PageGroupModel();

                    $pageGroupModel->update_page_group($page_group_data, $page_group_id);

                    $bulkUploadQueueModel->update_queue_status("COMPLETED", $queue_id);
                    Log::error("Completed the process for queue id => " . $queue_id);
                }
            } else {
                $bulkUploadQueueModel->update_queue_status("FILE_NOT_FOUND", $queue_id);
            }
        }
    }

    /**
     * @SWG\Get(path="/get_bulk_upload_request_list",
     *   tags={"Bulk Upload Page Group"},
     *   summary="Returns bulk upload data",
     *   description="Returns bulk upload  data",
     *   operationId="get_bulk_upload_request_list",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid request",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function get_bulk_upload_request_list() {

        $bulkUploadRequestModel = new BulkUploadRequestModel();
        $response_details = $bulkUploadRequestModel->get_request_list();
        $response_array = array("success" => TRUE, "data" => $response_details, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Get(path="/get_bulk_upload_details/{req_id}",
     *   tags={"Bulk Upload Page Group"},
     *   summary="Returns Bulk Upload details",
     *   description="Returns Bulk Upload details",
     *   operationId="get_bulk_upload_details",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="req_id",
     *     in="path",
     *     description="ID of the bulk upload to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function get_bulk_upload_details($request_id) {

        $bulkUploadRequestModel = new BulkUploadRequestModel();
        $details = $bulkUploadRequestModel->get_request_details($request_id);

        if ($details != NULL) {
            $bulkUploadQueueModel = new BulkUploadQueueModel();

            $queueDetails = $bulkUploadQueueModel->fetch_details_per_request($request_id);

            $response_details = array();
            $response_details['request_id'] = $request_id;
            $response_details['archive_file_name'] = $details->archive_file_name;
            $response_details['created_at'] = $details->created_at;

            if ($queueDetails != NULL) {
                $response_details['queue_details'] = $queueDetails;
            }
            $response_array = array("success" => TRUE, "data" => $response_details, "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        } else {
            $response_array = array("success" => FALSE, "data" => "Not Found", "errors" => array());
            return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
        }
    }

}
