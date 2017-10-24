<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class UsersModel extends Eloquent {

//    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $fillable = array('username', 'first_name', 'last_name', 'password', 'email',
        'activation_key', 'is_active', 'profile_image', 'is_forgot_initiated', 'is_verified');

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $hidden = ['password'];

    public function aunthenticate($login_data) {
        $user_data = UsersModel::where($login_data)->first();
        return $user_data;
    }

    public function user_details($query_details = NULL) {
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
            $user_data = UsersModel::where('username', 'like', "%$search_key%")
                    ->orWhere('first_name', 'like', "%$search_key%")
                    ->orWhere('last_name', 'like', "%$search_key%")
                    ->orWhere('email', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $user_data = UsersModel::skip($skip)->take($limit)->get();
        }
        return $user_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = UsersModel::where('username', 'like', "%$search_key%")
                    ->orWhere('first_name', 'like', "%$search_key%")
                    ->orWhere('last_name', 'like', "%$search_key%")
                    ->orWhere('email', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = UsersModel::count();
        }
        return $total_count;
    }

    public function activate_user($user_data) {
        $user_info = UsersModel::where($user_data)->first();
        $_id = $user_info['_id'];
        UsersModel::find($_id)->update(['is_active' => true,'is_verified' => true]);
        return 1;
    }

    public function get_random_user() {
        $user_data = UsersModel::all()->first();
        return $user_data;
    }

    public function find_user_details($user_id) {
        $user_info = UsersModel::find($user_id);
        return $user_info;
    }

    public function user_details_by_username($username) {
        $user_info = UsersModel::where('username', $username)->first();
        return $user_info;
    }

    public function update_user($user_id, $user_data) {
        $user_info = UsersModel::where('_id', $user_id)
                ->update($user_data);
        return $user_info;
    }

    public function is_verified_user($username) {
        $user_count = UsersModel::where(['username' => $username, 'is_verified' => TRUE])->count();
        return $user_count;
    }

}
