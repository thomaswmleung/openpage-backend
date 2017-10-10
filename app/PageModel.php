<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\MainModel;

class PageModel extends Eloquent {

    protected $collection = 'page';
    protected $fillable = array('overlay', 'main_id', 'background', 'remark', 'is_imported', 'preview_images');

    public function add_or_update_page($insert_data, $page_id = "") {
        if (!isset($insert_data['is_imported'])
                OR $insert_data['is_imported'] != TRUE) {
            $insert_data['is_imported'] = FALSE;
        }
        if ($page_id != null && $page_id != "") {
            PageModel::find($page_id)->update($insert_data);
            $result = PageModel::find($page_id);
        } else {
            $result = PageModel::create($insert_data);
        }



        return $result;
    }

    public function fetch_main_id($page_id) {
        $result = PageModel::find($page_id)->first();
        return $result->main_id;
    }

    public function get_page($page_id_array) {
        $page_data = PageModel::where($page_id_array)->first();
        return $page_data;
    }

    public function page_list($query_details = NULL) {
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
        $sort_by = $query_details['sort_by'];
        $order_by = $query_details['order_by'];

        if ($search_key != "") {
            $page_data = PageModel::where('remark', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $page_data = PageModel::skip($skip)->take($limit)->orderBy($sort_by, $order_by)->get();
        }
        return $page_data;
    }

    public static function get_page_details($page_id) {
        $page_details = array();
        $page_model_data = PageModel::find($page_id);

        if ($page_model_data != NULL) {
            $page_details['overlay'] = $page_model_data->overlay;
            $page_details['background'] = $page_model_data['background'];

            $main_id = $page_model_data->main_id;
            $main_details = MainModel::get_main_details($main_id);
            $page_model_data->main_details = $main_details;
            return $page_model_data;
        } else {
            return FALSE;
        }
    }

    public function page_search($page_data) {
        $search_key = $page_data['search_key'];
        $skip = $page_data['skip'];
        $limit = $page_data['limit'];
        $page_query = PageModel::where('remark', 'like', '%' . $search_key . '%');
        $page_query->skip($skip);
        $page_query->take($limit);
        return $page_query->get();
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = PageModel::where('remark', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = PageModel::count();
        }
        return $total_count;
    }

    public function find_page_details($page_id) {
        $page_data = PageModel::find($page_id);
        return $page_data;
    }

}
