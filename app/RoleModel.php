<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class RoleModel extends Eloquent {

    protected $collection = 'user_role';
    protected $fillable = array('role_name');


    

}
