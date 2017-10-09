<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller {

    static public function send_activation_mail($mail_data) {
        
        Mail::send('emails.activation_mail', $mail_data, function ($message) use ($mail_data) {
            $message->subject("Openpage account creation");
            $message->from($mail_data['from_email'], 'Openpage');
            $message->to($mail_data['to_email']);
        });

        return 1;
    }
    
    static public function forgot_password_mail($mail_data) {
        
        Mail::send('emails.forgot_password', $mail_data, function ($message) use ($mail_data) {
            $message->subject("Openpage : Forgot password link");
            $message->from('info@openpage.com', 'Openpage');
            $message->to($mail_data['email']);
        });

        return 1;
    }

}
