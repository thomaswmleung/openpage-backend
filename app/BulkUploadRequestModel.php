<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\MainModel;
use DateTime;

class BulkUploadRequestModel extends Eloquent {

    protected $collection = 'bulk_upload_request';
    protected $fillable = array('archive_file_name', 'created_by');

    public function create_request($requestArray) {
        $result = BulkUploadRequestModel::create($requestArray);
        return $result;
    }

    public function fetch_details($request_id) {
        if ($request_id != NULL AND $request_id != "") {
            $result = BulkUploadRequestModel::find($request_id);
            return $result;
        } else {
            return NULL;
        }
    }
    public function get_request_list() {
        
        return BulkUploadRequestModel::get();
        
    }
    public function get_request_details($request_id) {
        
        return BulkUploadRequestModel::find($request_id);
        
    }

}
