<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ParticularModel extends Eloquent {

    protected $collection = 'particular';
    protected $fillable = array('detail', 'title');

    public function create_particular($insert_data) {
        $result = ParticularModel::create($insert_data);
        return $result->_id;
    }

    public function find_particular_details($particular_id) {
        $particular_info = ParticularModel::find($particular_id);
        return $particular_info;
    }

    public function create_or_update_particular($insert_data, $id) {
        $result = ParticularModel::updateOrCreate(['_id' => $id], $insert_data);
        return $result->_id;
    }

    public function particular_details($query_details = NULL) {

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
            $particular_data = ParticularModel::
                    where('title', 'like', "%$search_key%")
                    ->orWhere('detail', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $particular_data = ParticularModel::skip($skip)->take($limit)->get();
        }
        return $particular_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = ParticularModel::where('title', 'like', "%$search_key%")
                    ->orWhere('detail', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = ParticularModel::count();
        }
        return $total_count;
    }

}
