<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PageGroupModel extends Eloquent {

    protected $collection = 'page_group';
    protected $fillable = array('page','preview_url');
    
    
     public function add_page_group($insert_data) {       
        $result = PageGroupModel::create($insert_data);
        return $result;
    }
     public function create_page_group() {       
        $result = PageGroupModel::create();
        return $result->_id;
    }

    public function update_page_group($update_data,$page_group_id){
        $result = PageGroupModel::find($page_group_id)->update($update_data);
    }

 
     public function page_group_details($page_group_array = NULL) {
        if ($page_group_array != NULL) {
            $page_group_data = PageGroupModel::where($page_group_array)->first();
        } else {
            $page_group_data = PageGroupModel::all();
        }
        return $page_group_data;
    }
    

}
