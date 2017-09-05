<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PageGroupModel extends Eloquent {

    protected $collection = 'page_group';
    protected $fillable = array('page', 'preview_url');

    public function add_page_group($insert_data) {
        $result = PageGroupModel::create($insert_data);
        return $result;
    }

    public function create_page_group() {
        $result = PageGroupModel::create();
        return $result->_id;
    }

    public function update_page_group($update_data, $page_group_id) {
        $result = PageGroupModel::find($page_group_id)->update($update_data);
    }

    public function getRandomDocument() {
        return PageGroupModel::all()->first();
    }

    public function page_group_details($query_details = NULL) {
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
            $page_group_data = PageGroupModel::where('preview_url', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $page_group_data = PageGroupModel::skip($skip)->take($limit)->get();
        }
        return $page_group_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = PageGroupModel::where('preview_url', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = PageGroupModel::count();
        }
        return $total_count;
    }

    public function find_page_group_details($page_group_id) {
        $page_group_info = PageGroupModel::find($page_group_id);
        return $page_group_info;
    }

}
