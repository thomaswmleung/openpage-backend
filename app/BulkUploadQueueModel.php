<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\MainModel;
use DateTime;

class BulkUploadQueueModel extends Eloquent {

    protected $collection = 'bulk_upload_queue';
    protected $fillable = array('bulk_request_id', 'import_file_name', 'page_group_title', 'page_group_subtitle', 'subject', 'domain',
        'sub_domain', 'teacher_copy', 'status','level_of_difficulty','level_of_scaffolding');

    public function add_to_queue($requestArray) {
        $result = BulkUploadQueueModel::create($requestArray);
        return $result;
    }

    public function fetch_pending_entity() {
        $req = array('status' => 'PENDING');
        $result = BulkUploadQueueModel::where('status', 'PENDING')->first();
        return $result;
    }

    public function update_queue_status($status,$queue_id) {

        $data = array('status' => $status);
        $result = BulkUploadQueueModel::find($queue_id)->update($data);
        return $result;
    }
    
    public function fetch_details_per_request($request_id) {

        $result = BulkUploadQueueModel::where('bulk_request_id',$request_id)->get();
        return $result;
    }

}
