<?php

    class EmailService
    {
        function __construct()
        {

        }

        public function send_password_reset_mail($to, $otp)
        {
            $template = R::findOne( 'email_template', ' title = ? ', [ 'password_reset' ] );
            $subject = $template->subject;
            $message = $template->template;
            $message = str_replace("#otp", $otp, $message);
            $message = str_replace("#email", $to, $message);

            // To send HTML mail, the Content-type header must be set
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
            $headers[] = 'From: mcQschools <noreply@mcqschools.com>';
        
            $result =  mail($to, $subject, $message, implode("\r\n", $headers));
            
            if($result) 
                return true;
            return false;
        }

    }

?>