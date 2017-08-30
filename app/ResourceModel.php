<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class ResourceModel extends Eloquent {

    protected $collection = 'resource';
    protected $fillable = array('title','description','type', 'url','remark', 'tag', 'created_by', 'updated_by');

//    public function resource_details($resource_array = NULL, $search_key = NULL) {
//        $resource_query = DB::collection('resource');
//        if ($resource_array != NULL) {
//            $resource_query->where($resource_array)->first();
//        } else {
//            if (isset($search_key) AND $search_key != "") {
//                $resource_query->orwhere('remark', 'like', '%' . $search_key . '%');
//                $resource_query->orwhere('tag', 'like', '%' . $search_key . '%');
//            }
//        }
//        return $resource_query->get();
//    }
    public function resource_details($data_array = NULL) {
        if ($data_array != NULL) {
            $result_data = ResourceModel::where($data_array)->first();
        } else {
            $result_data = ResourceModel::all();
        }
        return $result_data;
    }

    public function update_media($data) {
        $result = MediaModel::find($data['_id'])->update($data);
        return $result;
    }

    public function get_random_media() {
        return MediaModel::all()->first();
    }

}
