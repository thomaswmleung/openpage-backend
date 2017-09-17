<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class KeywordModel extends Eloquent {

    protected $collection = 'keyword';
    protected $fillable = array('keyword');

    public function create_or_update_keyword($insert_data, $id) {
        //$result = MainModel::create($insert_data);
        $result = KeywordModel::updateOrCreate(
                        ['_id' => $id], 
                        $insert_data
        );
        return $result->_id;
    }   
    
    public function getKeywordId($keyword) {
       $keyword_data = KeywordModel::where(array('keyword'=>'$keyword'));
       if($keyword_data == NULL){
           return NULL;
       }
       return $keyword_data->_id();
    }
}
