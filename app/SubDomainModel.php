<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SubDomainModel extends Eloquent {

    protected $collection = 'sub_domain';
    protected $fillable = array('code', 'title');

    public function create_sub_domain($insert_data) {
        $result = SubDomainModel::create($insert_data);
        return $result->_id;
    }

}
