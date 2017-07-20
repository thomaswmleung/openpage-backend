<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;

class OverlayModel extends Eloquent {

    protected $collection = 'overlay';
    protected $fillable = array('overlay');
    
    
     public function add_overlay($insert_data) {       
        $result = OverlayModel::create($insert_data);
        return $result;
    }


    

}
