<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ClassModel extends Eloquent {

    protected $collection = 'class';
    protected $fillable = array('class_name');

    public function create_or_update_class($insert_data, $id) {
        $result = ClassModel::updateOrCreate(
                        ['_id' => $id], $insert_data
        );
        return $result->_id;
    }

    public function class_details($query_details = NULL) {

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
            $class_data = ClassModel::where('class_name', 'like', "%$search_key%")->skip($skip)->take($limit)->get();
        } else {
            $class_data = ClassModel::skip($skip)->take($limit)->get();
        }
        return $class_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = ClassModel::where('class_name', 'like', "%$search_key%")->count();
        } else {
            $total_count = ClassModel::count();
        }
        return $total_count;
    }

    public function find_class_details($class_id) {
        $class_info = ClassModel::find($class_id);
        return $class_info;
    }

}
