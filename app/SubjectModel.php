<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SubjectModel extends Eloquent {

    protected $collection = 'subject';
    protected $fillable = array('code', 'title', 'domain');

    public function create_subject($insert_data, $main_id) {
        //$result = MainModel::create($insert_data);
        $result = SubjectModel::updateOrCreate(
                        ['_id' => $main_id], $insert_data
        );
        return $result;
    }

    public function subject_details($subject_array = NULL) {
        if ($subject_array != NULL) {
            $subject_data = SubjectModel::where($subject_array)->first();
        } else {
            $subject_data = SubjectModel::all();
        }
        return $subject_data;
    }

}
