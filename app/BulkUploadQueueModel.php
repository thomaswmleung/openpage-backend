<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\MainModel;
use DateTime;

class BulkUploadQueueModel extends Eloquent {

    protected $collection = 'bulk_upload_queue';
    protected $fillable = array('bulk_request_id', 'import_file_name','image_name', 'page_group_title', 'page_group_subtitle',
        'syllabus_code','codex','area', 'domain','sub_domain','knowledge_unit','learning_objective','particulars',
        'copyright_content','copyright_artwork','copyright_photo','linkage','user','level',
        'page_group_nature','page_group_position','page_group_output','row_reference','parent_reference','parent_page_id',
        'page_group_layout', 'status','level_of_difficulty','level_of_scaffolding', 'author', 'remark');

    public function add_to_queue($requestArray) {
        $result = BulkUploadQueueModel::create($requestArray);
        return $result;
    }

    public function fetch_pending_entity() {
        $req = array('status' => 'PENDING');
        $result = BulkUploadQueueModel::where('status', 'PENDING')->limit(20)->get();
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
