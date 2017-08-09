<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SubjectModel extends Eloquent {

    protected $collection = 'subject';
    protected $fillable = array('code', 'title','domain');

    public function create_subject($insert_data, $main_id) {       
        //$result = MainModel::create($insert_data);
         $result = SubjectModel::updateOrCreate(
                 ['_id' => $main_id],
                 $insert_data
                 );
        return $result;
    }

}
