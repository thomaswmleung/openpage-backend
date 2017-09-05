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

    public function subject_details($query_details = NULL) {
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
        }

        if ($search_key != "") {
            $subject_data = SubjectModel::where('code', 'like', "%$search_key%")
                    ->orWhere('title', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $subject_data = SubjectModel::skip($skip)->take($limit)->get();
        }
        return $subject_data;
    }

        public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = SubjectModel::where('code', 'like', "%$search_key%")
                    ->orWhere('title', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = SubjectModel::count();
        }
        return $total_count;
    }

    public function find_subject_details($subject_id) {
        $subject_data = SubjectModel::find($subject_id);
        return $subject_data;
    }
}
