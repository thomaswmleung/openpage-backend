<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DomainModel extends Eloquent {

    protected $collection = 'domain';
    protected $fillable = array('code', 'title');

    public function find_domain_details($domain_id) {
        $domain_info = DomainModel::find($domain_id);
        return $domain_info;
    }

    public function create_domain($insert_data) {
        $result = DomainModel::create($insert_data);
        return $result->_id;
    }
    
    public function create_or_update_domain($insert_data, $id) {
        $result = DomainModel::updateOrCreate(['_id' => $id], $insert_data);
        return $result->_id;
    }
    
    public function domain_details($query_details = NULL) {

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
            $domain_data = DomainModel::
                    where('title', 'like', "%$search_key%")
                    ->orWhere('code', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $domain_data = DomainModel::skip($skip)->take($limit)->get();
        }
        return $domain_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = DomainModel::where('title', 'like', "%$search_key%")->count();
        } else {
            $total_count = DomainModel::count();
        }
        return $total_count;
    }

}
