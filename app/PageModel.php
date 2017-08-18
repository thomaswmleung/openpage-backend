<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\MainModel;

class PageModel extends Eloquent {

    protected $collection = 'page';
    protected $fillable = array('overlay', 'main_id', 'background', 'remark');

    public function add_page($insert_data) {
        $result = PageModel::create($insert_data);
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

    public function page_list($search_key = NULL, $skip = NULL, $limit = NULL) {
        $page_data = PageModel::where($search_key)->skip($skip)->take($limit)->get();
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
//        $main_details = MainModel::get
    }

}
