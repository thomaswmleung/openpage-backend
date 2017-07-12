<?php

namespace App;

use Jenssegers\Mongodb\Model as Eloquent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\RoleModel;

class UsersModel extends Eloquent {

    protected $collection = 'users';
    protected $fillable = array('first_name', 'last_name', 'email', 'password', 'contact_number',
        'resume_text', 'resume_file', 'role_id');

    use SoftDeletes;

    protected $dates = ['deleted_at'];

//    protected $hidden = ['password'];

    public function register_user($users_data) {



        $role_data = RoleModel::where('role_name', 'candidate')->first();

        $insert_data = array(
            'role_id' => $role_data['_id'],
            'first_name' => $users_data['first_name'],
            'last_name' => $users_data['last_name'],
            'contact_number' => $users_data['contact_number'],
            'email' => $users_data['email'],
            'password' => $users_data['password'],
            'resume_file' => $users_data['resume_file'],
            'resume_text' => $users_data['resume_text'],
        );

        $users_info = UsersModel::create($insert_data);
        return $users_info;
    }

    public function check_unique_user_email($email) {
        $user_count = UsersModel::where('email', $email)->count();
        return $user_count;
    }

    public function check_unique_email($user_data) {
        $count = UsersModel::where('email', $user_data['email'])->count();
        return $count;
    }

    public function login_check($email, $password) {
        $count = UsersModel::where(array('email' => $email, 'password' => $password))->count();
        if ($count == 1) {

            $admin_data = $this->user_details($email);
            $admin_data['count'] = $count;
        } else {
            $admin_data['count'] = $count;
        }
        return $admin_data;
    }

    public function user_details($email) {
        $admin_data = UsersModel::where(array('email' => $email))->first();
        return $admin_data;
    }

}
