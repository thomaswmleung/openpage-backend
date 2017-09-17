<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ClassFlowModel extends Eloquent {

    protected $collection = 'class_flow';
    protected $fillable = array('class_id','title','page_id','resource_ids');

    public function create_or_update_class_flow($insert_data, $id) {
        $result = ClassFlowModel::updateOrCreate(
                        ['_id' => $id], $insert_data
        );
        return $result->_id;
    }

    public function class_flow_details($query_details = NULL) {

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
            $class_flow_data = ClassFlowModel::where('title', 'like', "%$search_key%")->skip($skip)->take($limit)->get();
        } else {
            $class_flow_data = ClassFlowModel::skip($skip)->take($limit)->get();
        }
        return $class_flow_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = ClassFlowModel::where('title', 'like', "%$search_key%")->count();
        } else {
            $total_count = ClassFlowModel::count();
        }
        return $total_count;
    }

    public function find_class_flow_details($class_flow_id) {
        $class_flow_info = ClassFlowModel::find($class_flow_id);
        return $class_flow_info;
    }

}
