<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class StaticHtmlPageModel extends Eloquent {

    protected $collection = 'static_html_page';
    protected $fillable = array('page_code', 'content', 'created_by', 'updated_by');

    public function create_or_update_static_html_page($insert_data, $id) {
        $result = StaticHtmlPageModel::updateOrCreate(
                        ['_id' => $id], $insert_data
        );
        return $result->_id;
    }

    public function static_html_page_details($query_details = NULL) {

        $skip = 0;
        $limit = config('constants.default_query_limit');
        $search_key = "";

        if (isset($query_details['skip'])) {
            $skip = $query_details['skip'];
        }
        if (isset($query_details['limit'])) {
            $limit = $query_details['limit'];
        }
        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        }

        if ($search_key != "") {
            $static_html_page_data = StaticHtmlPageModel::
                            where('content', 'like', "%$search_key%")
                            ->orWhere('page_code', 'like', "%$search_key%")
                            ->skip($skip)->take($limit)->get();
        } else {
            $static_html_page_data = StaticHtmlPageModel::skip($skip)->take($limit)->get();
        }
        return $static_html_page_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = StaticHtmlPageModel::
                    where('content', 'like', "%$search_key%")
                    ->orWhere('page_code', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = StaticHtmlPageModel::count();
        }
        return $total_count;
    }

    public function find_static_html_page_details($static_html_page_id) {
        $static_html_page_info = StaticHtmlPageModel::find($static_html_page_id);
        return $static_html_page_info;
    }

}
