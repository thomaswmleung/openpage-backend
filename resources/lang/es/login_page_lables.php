<?php

$genericLabels = array();
$genericLabels['edit'] = trans('generic_lables.edit');
$genericLabels['delete'] = trans('generic_lables.delete');
$genericLabels['save'] = trans('generic_lables.save');
$genericLabels['close'] = trans('generic_lables.close');
$genericLabels['submit'] = trans('generic_lables.submit');




$genericLabels['sign_out'] = trans('generic_lables.sign_out');
$genericLabels['sign_in'] = trans('generic_lables.sign_in');


$labels_array = array();
$labels_array = [

    'membership_registration' => 'Registrar una nueva candidato',
    'session_start' => 'Firmar En',
    'first_name' => "Nombre de pila",
    'last_name' => "Apellido",
    'password' => "Contraseña",
    "email" => "Email dirección",
    "contact_number" => "Número de contacto",
    'retype_password' => "Vuelva a escribir la contraseña",
    'attach_resume' => "Adjuntar curriculum vitae",
    'resume' => "Currículum",
    'register' => "Registro",
    'email_address' => "Dirección de correo electrónico",
    
    
    "js_msgs" => array(
        "email_exist_error" => "Este identificador de correo electrónico ya existe.",
    ),
];

return array_merge($labels_array, $genericLabels);
