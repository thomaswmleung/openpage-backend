<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class LayoutModel extends Eloquent {

    protected $collection = 'layout';
    protected $fillable = array('layout_code', 'overlay', 'main', 'background', 'front_cover', 'table_of_content','back_cover');

       
     public function create_or_update_layout($insert_data, $id) {       
         $result = LayoutModel::updateOrCreate(
                 ['_id' => $id],
                 $insert_data
                 );
        return $result->_id;
    }
    


}
