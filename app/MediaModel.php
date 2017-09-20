<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class MediaModel extends Eloquent {

    protected $collection = 'media';
    protected $fillable = array('type', 'extension', 'url', 'right', 'usage', 'remark', 'tag', 'created_by', 'updated_by');

    public function media_details($query_details = NULL) {
        if ($query_details == NULL) {
            $skip = 0;
            $limit = config('constants.default_query_limit');
            $search_key = "";
        } else {
            if (isset($query_details['skip'])) {
                $skip = $query_details['skip'];
            } else {
                $skip = 0;
            }
            if (isset($query_details['limit'])) {
                $limit = $query_details['limit'];
            } else {
                $limit = config('constants.default_query_limit');
            }
            if (isset($query_details['search_key'])) {
                $search_key = $query_details['search_key'];
            } else {
                $search_key = "";
            }
            if (isset($query_details['user_id'])) {
                $user_id = $query_details['user_id'];
            } else {
                $user_id = "";
            }
        }

        if ($search_key != "" || $user_id != "") {
            $media_data = MediaModel::
                    Where(function($userIdQuery)use ($user_id) {
                        if ($user_id != "") {
                            $userIdQuery->where('created_by', $user_id);
                        }
                    })
                    ->Where(function($filterSeaarchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $filterSeaarchQuery->where('remark', 'like', "%$search_key%")
                            ->orWhere('tag', 'like', "%$search_key%")
                            ->orWhere('type', 'like', "%$search_key%")
                            ->orWhere('extension', 'like', "%$search_key%");
                        }
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $media_data = MediaModel::skip($skip)->take($limit)->get();
        }
        return $media_data;
    }

    public function total_count($query_details) {
        $search_key = $query_details['search_key'];
        $user_id = $query_details['user_id'];
        if ($search_key != "" || $user_id != "") {
            $total_count = MediaModel::
                    Where(function($userIdQuery)use ($user_id) {
                        if ($user_id != "") {
                            $userIdQuery->where('created_by', $user_id);
                        }
                    })
                    ->Where(function($filterSeaarchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $filterSeaarchQuery->where('remark', 'like', "%$search_key%")
                            ->orWhere('tag', 'like', "%$search_key%")
                            ->orWhere('type', 'like', "%$search_key%")
                            ->orWhere('extension', 'like', "%$search_key%");
                        }
                    })
                    ->count();
        } else {
            $total_count = MediaModel::count();
        }
        return $total_count;
    }

    public function update_media($data) {
        $result = MediaModel::find($data['_id'])->update($data);
        return $result;
    }

    public function get_random_media() {
        return MediaModel::all()->first();
    }

    public function find_media_details($media_id) {
        $media_info = MediaModel::find($media_id);
        return $media_info;
    }

}
