<?php

$genericLabels = array();
$genericLabels['edit'] = trans('generic_lables.edit');
$genericLabels['delete'] = trans('generic_lables.delete');
$genericLabels['save'] = trans('generic_lables.save');
$genericLabels['close'] = trans('generic_lables.close');
$genericLabels['submit'] = trans('generic_lables.submit');




$genericLabels['sign_out'] = trans('generic_lables.sign_out');
$genericLabels['sign_in'] = trans('generic_lables.sign_in');
$genericLabels['sign_up'] = trans('generic_lables.sign_up');


$labels_array = array();
$labels_array = [

    'membership_registration' => 'Register a new candidate',
    'session_start' => 'Sign In',
    'first_name' => "First Name",
    'last_name' => "Last Name",
    'password' => "Password",
    "email" => "Email address",
    "contact_number" => "Contact Number",
    'retype_password' => "Retype Password",
    'attach_resume' => "Attach Resume",
    'resume' => "Resume",
    'register' => "Register",
    'email_address' => "Email address",
    
    
    "js_msgs" => array(
        "email_exist_error" => "This email id already exist.",
    ),
];

return array_merge($labels_array, $genericLabels);
