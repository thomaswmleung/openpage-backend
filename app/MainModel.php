<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MainModel extends Eloquent {

    protected $collection = 'main';
    protected $fillable = array('header_text','footer_text','section');
    
    
     public function add_main($insert_data, $main_id) {       
        //$result = MainModel::create($insert_data);
         $result = MainModel::updateOrCreate(
                 ['_id' => $main_id],
                 $insert_data
                 );
        return $result;
    }


    

}
