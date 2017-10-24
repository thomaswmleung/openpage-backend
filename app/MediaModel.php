<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;
use DateTime;
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
            if (isset($query_details['type'])) {
                $type = $query_details['type'];
            } else {
                $type = "";
            }
            if (isset($query_details['remark'])) {
                $remark = $query_details['remark'];
            } else {
                $remark = "";
            }
            if (isset($query_details['extension'])) {
                $extension = $query_details['extension'];
            } else {
                $extension = "";
            }
            
        }
        $tag_array = $query_details['tag'];
        $from_date = $query_details['from_date'];
        $to_date = $query_details['to_date'];
        $sort_by = $query_details['sort_by'];
        $order_by = $query_details['order_by'];

        if ($search_key != "" || $user_id != "" || $type != "" || $remark != "" || $extension != "" || $from_date != "" || count($tag_array)>0) {
            $media_data = MediaModel::
                    Where(function($userIdQuery)use ($query_details) {
                        if ($query_details['user_id'] != "") {
                            $userIdQuery->where('created_by', 'like',$query_details['user_id']);
                        }
                        if ($query_details['type'] != "") {
                            $userIdQuery->where('type', 'like',$query_details['type']);
                        }
                        if ($query_details['remark'] != "") {
                            $userIdQuery->where('remark', 'like',$query_details['remark']);
                        }
                        if ($query_details['extension'] != "") {
                            $userIdQuery->where('extension','like', $query_details['extension']);
                        }
                        if (count($query_details['tag'])>0) {
                            // use foreach for tags array to use like query for case insensitive search using orWhere
                            $userIdQuery->where('tag','all',$query_details['tag']);
                        }
                        if ($query_details['from_date'] != "") {
                            $start = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $userIdQuery->whereBetween('created_at', array($start, $stop));
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
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $media_data = MediaModel::skip($skip)->take($limit)->orderBy($sort_by, $order_by)->get();
        }
        return $media_data;
    }

    public function total_count($query_details) {
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
        if (isset($query_details['type'])) {
            $type = $query_details['type'];
        } else {
            $type = "";
        }
        if (isset($query_details['remark'])) {
            $remark = $query_details['remark'];
        } else {
            $remark = "";
        }
        if (isset($query_details['extension'])) {
            $extension = $query_details['extension'];
        } else {
            $extension = "";
        }
      
        $tag_array = $query_details['tag'];
        $from_date = $query_details['from_date'];
        $to_date = $query_details['to_date'];
        if ($search_key != "" || $user_id != "" || $type != "" || $remark != "" || $extension != "" || $from_date != "" || count($tag_array)>0) {
            $total_count = MediaModel::
                    Where(function($userIdQuery)use ($query_details) {
                        if ($query_details['user_id'] != "") {
                            $userIdQuery->where('created_by','like', $query_details['user_id']);
                        }
                        if ($query_details['type'] != "") {
                            $userIdQuery->where('type','like', $query_details['type']);
                        }
                        if ($query_details['remark'] != "") {
                            $userIdQuery->where('remark','like', $query_details['remark']);
                        }
                        if ($query_details['extension'] != "") {
                            $userIdQuery->where('extension','like', $query_details['extension']);
                        }
                        if (count($query_details['tag'])>0) {
                            $userIdQuery->where('tag','all', $query_details['tag']);
                        }
                        if ($query_details['from_date'] != "") {
                            $start = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $userIdQuery->whereBetween('created_at', array($start, $stop));
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
