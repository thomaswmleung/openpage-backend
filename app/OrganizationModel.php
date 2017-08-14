<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class OrganizationModel extends Eloquent {

    protected $collection = 'organization';
    protected $fillable = array('name', 'address','email','contact_person','type','user_id','logo','remark','consultant','role');

    public function organization_details($data_array = NULL) {
        if ($data_array != NULL) {
            $result_data = OrganizationModel::where($data_array)->first();
        } else {
            $result_data = OrganizationModel::all();
        }
        return $result_data;
    }

}
