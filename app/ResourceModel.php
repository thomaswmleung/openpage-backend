<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class ResourceModel extends Eloquent {

    protected $collection = 'resource';
    protected $fillable = array('title', 'description', 'type', 'url', 'remark', 'tag', 'created_by', 'updated_by');

    public function resource_details($query_details = NULL) {
        if ($query_details == NULL) {
            $skip = 0;
            $limit = config('constants.default_query_limit');
            $search_key = "";
        } else {
            if (isset($query_details['skip'])) {
                $skip = $query_details['skip'];
            } else {
                $skip = 0;
            }
            if (isset($query_details['limit'])) {
                $limit = $query_details['limit'];
            } else {
                $limit = config('constants.default_query_limit');
            }
            if (isset($query_details['search_key'])) {
                $search_key = $query_details['search_key'];
            } else {
                $search_key = "";
            }
        }

        if ($search_key != "") {
            $resource_data = ResourceModel::where('title', 'like', "%$search_key%")
                    ->orWhere('description', 'like', "%$search_key%")
                    ->orWhere('type', 'like', "%$search_key%")
                    ->orWhere('remark', 'like', "%$search_key%")
                    ->orWhere('tag', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $resource_data = ResourceModel::skip($skip)->take($limit)->get();
        }
        return $resource_data;
    }

    public function resource_data($resource_id) {
        $result_data = ResourceModel::find($resource_id);
        return $result_data;
    }

    public function add_or_edit_resource($resource_data, $resource_id) {
        $result = ResourceModel::updateOrCreate(['_id' => $resource_id], $resource_data);
        return $result;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = ResourceModel::where('title', 'like', "%$search_key%")
                    ->orWhere('description', 'like', "%$search_key%")
                    ->orWhere('type', 'like', "%$search_key%")
                    ->orWhere('remark', 'like', "%$search_key%")
                    ->orWhere('tag', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = ResourceModel::count();
        }
        return $total_count;
    }

}
