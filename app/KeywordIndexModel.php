<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class KeywordIndexModel extends Eloquent {

    protected $collection = 'keyword_index';
    protected $fillable = array('keyword_id','document_id','type');

    public function create_or_update_keyword_index($insert_data, $id) {
        //$result = MainModel::create($insert_data);
        $result = KeywordIndexModel::updateOrCreate(
                        ['keyword_id' => $id], 
                        $insert_data
        );
        return $result->_id;
    }

}
