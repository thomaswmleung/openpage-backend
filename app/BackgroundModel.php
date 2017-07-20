<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;

class BackgroundModel extends Eloquent {

    protected $collection = 'background';
    protected $fillable = array('background');
    
    
     public function add_back_ground($insert_data) {       
        $result = BackgroundModel::create($insert_data);
        return $result;
    }


    

}
