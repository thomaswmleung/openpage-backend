<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class MediaModel extends Eloquent {

    protected $collection = 'media';
    protected $fillable = array('type', 'extension', 'url', 'owner', 'right', 'usage', 'remark', 'tag', 'created_by', 'updated_by');

    public function media_details($media_array = NULL, $search_key = NULL) {
//        $media_query = MediaModel::all();
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

    public function update_media($data) {
        $result = MediaModel::find($data['_id'])->update($data);
        return $result;
    }

    public function get_random_media() {
        return MediaModel::all()->first();
    }

}
