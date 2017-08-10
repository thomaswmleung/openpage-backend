<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DomainModel extends Eloquent {

    protected $collection = 'domain';
    protected $fillable = array('code', 'title');

    public function create_domain($insert_data) {
        $result = DomainModel::create($insert_data);
        return $result->_id;
    }

}
