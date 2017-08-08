<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class BookModel extends Eloquent {

    protected $collection = 'book';
    protected $fillable = array('page', 'toc', 'cover', 'syllabus', 'keyword', 'organisation','created_by','updated_by');

    public function create_book($insert_data) {
        $result = BookModel::create($insert_data);
        return $result->_id;
    }

}
