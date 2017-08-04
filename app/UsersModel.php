<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class UsersModel extends Eloquent {

//    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $fillable = array('username', 'first_name', 'last_name', 'password', 'email','activation_key','is_active');

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
    
    public function activate_user($user_data) {
        $user_info = UsersModel::where($user_data)->first();
        $_id = $user_info['_id'];
        UsersModel::find($_id)->update(['is_active' => true]);
        return 1;
    }
    
    
    public function get_random_user(){
       $user_data = UsersModel::all()->first();
       return $user_data;
    }
}
