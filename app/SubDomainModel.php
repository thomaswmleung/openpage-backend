<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SubDomainModel extends Eloquent {

    protected $collection = 'sub_domain';
    protected $fillable = array('code', 'title');

    public function create_sub_domain($insert_data) {
        $result = SubDomainModel::create($insert_data);
        return $result->_id;
    }

    public function find_sub_domain_details($sub_domain_id) {
        $sub_domain_info = SubDomainModel::find($sub_domain_id);
        return $sub_domain_info;
    }

    public function create_or_update_sub_domain($insert_data, $id) {
        $result = SubDomainModel::updateOrCreate(['_id' => $id], $insert_data);
        return $result->_id;
    }

    public function sub_domain_details($query_details = NULL) {

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
            $sub_domain_data = SubDomainModel::
                    where('title', 'like', "%$search_key%")
                    ->orWhere('code', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $sub_domain_data = SubDomainModel::skip($skip)->take($limit)->get();
        }
        return $sub_domain_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = SubDomainModel::where('title', 'like', "%$search_key%")
                    ->orWhere('code', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = SubDomainModel::count();
        }
        return $total_count;
    }

}
