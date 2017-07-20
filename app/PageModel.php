<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;

class PageModel extends Eloquent {

    protected $collection = 'page';
    protected $fillable = array('ovelay','main','background','remark');
    
    
     public function add_page($insert_data) {       
        $result = PageModel::create($insert_data);
        return $result;
    }


    

}
