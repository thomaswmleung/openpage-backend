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
    
     public function layout_details($layout_array = NULL) {
        if ($layout_array != NULL) {
            $layout_data = LayoutModel::where($layout_array)->first();
        } else {
            $layout_data = LayoutModel::all();
        }
        return $layout_data;
    }


}
