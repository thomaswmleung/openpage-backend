<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class QuestionTypeModel extends Eloquent {

    protected $collection = 'question_type';
    protected $fillable = array('type', 'block');

    public function media_details($media_array = NULL, $search_key = NULL) {
        $media_query = DB::collection('media');
        if ($media_array != NULL) {
            $media_query->where($media_array)->first();
        } else {
            if (isset($search_key) AND $search_key != "") {
                $media_query->orwhere('remark', 'like', '%' . $search_key . '%');
                $media_query->orwhere('tag', 'like', '%' . $search_key . '%');
            }
        }
        return $media_query->get();
    }

    public function update_question_type($data) {
        $result = MediaModel::find($data['_id'])->update($data);
        return $result;
    }

    public function get_random_media() {
        return MediaModel::all()->first();
    }

}
