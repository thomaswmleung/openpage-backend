<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class UsersModel extends Eloquent {

    protected $collection = 'users';
    protected $fillable = array('username', 'first_name', 'last_name', 'password', 'email');

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $hidden = ['password'];

    public function aunthenticate($login_data) {
        $user_data = UsersModel::where($login_data)->first();
        return $user_data;
    }

    public function user_details($user_id = NULL) {
        if ($user_id != NULL) {
            $user_data = UsersModel::where($user_id)->first();
        } else {
            $user_data = UsersModel::all();
        }
        return $user_data;
    }

}
