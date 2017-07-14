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
    protected $hidden = ['password'];

    

    /* param 1: email 
      param 2: password

      Response :
      if user exist it return user data along with count
      else
      returns count 0;

     */

    public function aunthenticate($email, $password) {
        $count = UsersModel::where(array('email' => $email, 'password' => $password))->count();
        if ($count == 1) {
            $user_data = $this->user_details($email);
            $user_data['count'] = $count;
        } else {
            $user_data['count'] = $count;
        }
        return $user_data;
    }

    public function user_details($email) {
        $user_data = UsersModel::where(array('email' => $email))->first();
        return $user_data;
    }

}
