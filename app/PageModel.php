<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\MainModel;

class PageModel extends Eloquent {

    protected $collection = 'page';
    protected $fillable = array('overlay', 'main_id', 'background', 'remark');

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

    public function page_list($data_array = NULL) {
        if (isset($data_array['_id']) && $data_array['_id'] != NULL) {
            $page_id = $data_array['_id'];
            return PageModel::find($page_id)->first();
        } else {
            return PageModel::all();
             
        }
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

}
