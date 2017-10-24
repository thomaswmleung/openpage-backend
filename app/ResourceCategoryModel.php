<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ResourceCategoryModel extends Eloquent {

    protected $collection = 'resource_category';
    protected $fillable = array('resource_category');

    public function create_or_update_resource_category($insert_data, $id) {
        $result = ResourceCategoryModel::updateOrCreate(
                        ['_id' => $id], $insert_data
        );
        return $result->_id;
    }

    public function resource_category_details($query_details = NULL) {

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
            $resource_category_data = ResourceCategoryModel::where('resource_category', 'like', "%$search_key%")->skip($skip)->take($limit)->get();
        } else {
            $resource_category_data = ResourceCategoryModel::skip($skip)->take($limit)->get();
        }
        return $resource_category_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = ResourceCategoryModel::where('resource_category', 'like', "%$search_key%")->count();
        } else {
            $total_count = ResourceCategoryModel::count();
        }
        return $total_count;
    }

    public function find_resource_category_details($resource_category_id) {
        $resource_category_info = ResourceCategoryModel::find($resource_category_id);
        return $resource_category_info;
    }

}
