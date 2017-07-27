<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

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

}
