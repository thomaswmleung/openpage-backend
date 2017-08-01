<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MediaModel extends Eloquent {

    protected $collection = 'media';
    protected $fillable = array('type', 'extension', 'url', 'owner', 'right','usage','created_by','updated_by');
    
    public function media_details($media_array = NULL) {
        if ($media_array != NULL) {
            $media_data = MediaModel::where($media_array)->first();
        } else {
            $media_data = MediaModel::all();
        }
        return $media_data;
    }
    public function update_media($data) {
        $result = MediaModel::find($data['_id'])->update($data);
        return $result;
    }
   

}
