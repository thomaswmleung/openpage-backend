<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class BookModel extends Eloquent {

    protected $collection = 'book';
    protected $fillable = array('page', 'toc', 'cover', 'syllabus', 'keyword', 'organisation','created_by','updated_by');

       
     public function create_book($insert_data, $main_id) {       
        //$result = MainModel::create($insert_data);
         $result = BookModel::updateOrCreate(
                 ['_id' => $main_id],
                 $insert_data
                 );
        return $result->_id;
    }

}
