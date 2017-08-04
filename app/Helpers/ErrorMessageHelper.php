<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Log;
class ErrorMessageHelper{
    public static function getErrorMessage($error_code){
        return  config('error_messages'.".".$error_code);
    }
    public static function formulateErrorMessages($err_msgs_array){
        
        $messages = array();
        
        foreach ($err_msgs_array as $key => $value){
            $messages[$key] = json_encode(array($value
                                                    => config('error_messages'.".".$value)));
            
        }
        return $messages;
    }
    public static function getResponseErrorMessages($errors){
            $errors_array = $errors->messages();
            
            $response_error_array = array();
            $error_index = 0;
            
            
            foreach($errors_array as $key=> $err_msgs_array){
                 
                
                foreach ($err_msgs_array as $err_details_json){
                    
                    $error_detail_array = json_decode($err_details_json,true);
                    
                    foreach($error_detail_array as $code => $msg){

                        $response_error_array[$error_index]['ERR_CODE'] = $code;
                        $response_error_array[$error_index]['ERR_MSG'] = $msg;
                    }
                }
                $error_index++;
            }
            
            return $response_error_array;
    }
}