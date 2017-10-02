<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class OrganizationModel extends Eloquent {

    protected $collection = 'organization';
    protected $fillable = array('name', 'address', 'email', 'contact_person', 'type', 'user_id', 'logo', 'remark', 'consultant', 'role');

    public function organization_details($query_details = NULL) {
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
            $organization_info = OrganizationModel::where('name', 'like', "%$search_key%")
                    ->orWhere('type', 'like', "%$search_key%")
                    ->orWhere('remark', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $organization_info = OrganizationModel::skip($skip)->take($limit)->get();
        }
        return $organization_info;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = OrganizationModel::where('name', 'like', "%$search_key%")
                    ->orWhere('type', 'like', "%$search_key%")
                    ->orWhere('remark', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = OrganizationModel::count();
        }
        return $total_count;
    }

    public function find_organization_details($organization_id) {
        $organization_info = OrganizationModel::find($organization_id);
        return $organization_info;
    }
    
    public function create_or_update_organization($organization_array, $organization_id) {
        $result = OrganizationModel::updateOrCreate(['_id' => $organization_id], $organization_array);
        return $result;
    }

}
