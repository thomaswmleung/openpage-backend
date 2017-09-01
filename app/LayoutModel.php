<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class LayoutModel extends Eloquent {

    protected $collection = 'layout';
    protected $fillable = array('layout_code', 'overlay', 'main', 'background', 'front_cover', 'table_of_content', 'back_cover');

    public function create_or_update_layout($insert_data, $id) {
        $result = LayoutModel::updateOrCreate(
                        ['_id' => $id], $insert_data
        );
        return $result->_id;
    }

    public function layout_details($query_details = NULL) {

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
            $layout_data = LayoutModel::where('layout_code', 'like', "%$search_key%")->skip($skip)->take($limit)->get();
        } else {
            $layout_data = LayoutModel::skip($skip)->take($limit)->get();
        }
        return $layout_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = LayoutModel::where('layout_code', 'like', "%$search_key%")->count();
        } else {
            $total_count = LayoutModel::count();
        }
        return $total_count;
    }

    public function find_layout_details($layout_id) {
        $layout_info = LayoutModel::find($layout_id);
        return $layout_info;
    }

}
