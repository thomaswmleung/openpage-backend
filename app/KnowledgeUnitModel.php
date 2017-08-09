<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class KnowledgeUnitModel extends Eloquent {

    protected $collection = 'knowledge_unit';
    protected $fillable = array('code', 'title');

    
    public function create_knowledge_unit($insert_data) {
        $result = KnowledgeUnitModel::create($insert_data);
        return $result->_id;
    }   

}
