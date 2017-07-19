<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;

class MainModel extends Eloquent {

    protected $collection = 'main';
    protected $fillable = array('header_text','footer_text','section');
    
    
     public function add_main($insert_data) {       
        $result = MainModel::create($insert_data);
        return $result;
    }


    

}
