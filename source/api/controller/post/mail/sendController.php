<?php

/**
 * Basic Authentication for Application
 * 
 * 
 * @method new
 * @method login
 * @method logout
 * @method checkToken
 */
use \System\Model\Controller;

class SendController extends Controller
{
    public function phpMailer() {
        $to = $_POST['to'];
        $subject = "hello world";
        $message = " This is a message from Lam";

        $from_user = "=?UTF-8?B?".base64_encode($from_user)."?=";
        $subject = "=?UTF-8?B?".base64_encode($subject)."?=";
  
        $headers = "From: $from_user <$from_email>\r\n". 
                 "MIME-Version: 1.0" . "\r\n" . 
                 "Content-type: text/html; charset=UTF-8" . "\r\n"; 


        $this->json->sendBack([
            'success' => mail($to, $subject, $message, $headers),
            'to' => $_POST['to']
        ]);
        // return mail($to, $subject, $message, $headers); 
    }
}
