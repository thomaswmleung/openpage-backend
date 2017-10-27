<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;
use DateTime;
class CodexModel extends Eloquent {

    protected $collection = 'codex';
    protected $fillable = array('name', 'description', 'codex_image', 'label', 'created_by', 'updated_by');

    public function codex_details($query_details = NULL) {
       
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
            if (isset($query_details['name'])) {
                $name = $query_details['name'];
            } else {
                $name = "";
            }
            if (isset($query_details['description'])) {
                $description = $query_details['description'];
            } else {
                $description = "";
            }
            if (isset($query_details['label'])) {
                $label = $query_details['label'];
            } else {
                $label = "";
            }
            
        }
        $from_date = $query_details['from_date'];
        $to_date = $query_details['to_date'];
        $sort_by = $query_details['sort_by'];
        $order_by = $query_details['order_by'];
        
        if ($search_key != "" || $user_id != "" || $name != "" || $description != "" || $label != "" || $from_date != "") {
            
            $codex_data = CodexModel::
                    Where(function($userIdQuery)use ($query_details) {
                        if ($query_details['user_id'] != "") {
                            $userIdQuery->where('created_by', 'like',$query_details['user_id']);
                        }
                        if ($query_details['name'] != "") {
                            $userIdQuery->where('name', 'like',$query_details['name']);
                        }
                        if ($query_details['description'] != "") {
                            $userIdQuery->where('description', 'like',$query_details['description']);
                        }
                        if ($query_details['label'] != "") {
                            $userIdQuery->where('label','like', $query_details['label']);
                        }
                        if ($query_details['from_date'] != "") {
                            $start = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $userIdQuery->whereBetween('created_at', array($start, $stop));
                        }
                    })
                    ->Where(function($filterSeaarchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $filterSeaarchQuery->where('name', 'like', "%$search_key%")
                            ->orWhere('description', 'like', "%$search_key%")
                            ->orWhere('label', 'like', "%$search_key%");
                        }
                        
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $codex_data = CodexModel::skip($skip)->take($limit)->orderBy($sort_by, $order_by)->get();
        }
   
        return $codex_data;
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
         if (isset($query_details['name'])) {
                $name = $query_details['name'];
            } else {
                $name = "";
            }
            if (isset($query_details['description'])) {
                $description = $query_details['description'];
            } else {
                $description = "";
            }
            if (isset($query_details['label'])) {
                $label = $query_details['label'];
            } else {
                $label = "";
            }
      
        $from_date = $query_details['from_date'];
        $to_date = $query_details['to_date'];
        if ($search_key != "" || $user_id != "" || $name != "" || $description != "" || $label != "" || $from_date != "" ) {
            $total_count = CodexModel::
                    Where(function($userIdQuery)use ($query_details) {
//                        if ($query_details['user_id'] != "") {
//                            $userIdQuery->where('created_by','like', $query_details['user_id']);
//                        }
//                        if ($query_details['name'] != "") {
//                            $userIdQuery->where('name','like', $query_details['name']);
//                        }
//                        if ($query_details['description'] != "") {
//                            $userIdQuery->where('description','like', $query_details['description']);
//                        }
//                        if ($query_details['label'] != "") {
//                            $userIdQuery->where('label','like', $query_details['label']);
//                        }
                        if ($query_details['from_date'] != "") {
                            $start = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $userIdQuery->whereBetween('created_at', array($start, $stop));
                        }
                    })
                    ->Where(function($filterSeaarchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $filterSeaarchQuery->where('name', 'like', "%$search_key%")
                            ->orWhere('description', 'like', "%$search_key%")
                            ->orWhere('label', 'like', "%$search_key%");
                        }
                    })
                    ->count();
        } else {
            $total_count = CodexModel::count();
        }
        return $total_count;
    }

    public function update_codex($data) {
        $result = CodexModel::find($data['_id'])->update($data);
        return $result;
    }

    public function get_random_codex() {
        return CodexModel::all()->first();
    }

    public function find_codex_details($codex_id) {
        $codex_info = CodexModel::find($codex_id);
        return $codex_info;
    }

}
