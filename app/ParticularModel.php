<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ParticularModel extends Eloquent {

    protected $collection = 'particular';
    protected $fillable = array('detail', 'title');

    
    public function create_particular($insert_data) {
        $result = ParticularModel::create($insert_data);
        return $result->_id;
    }   

}
