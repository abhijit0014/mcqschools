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
            $headers[] = 'From: mcqschools <noreply@mcqschools.com>';
            //$headers[] = 'From: mcqschools <info@mcqschools.com>';
        
            $result =  mail($to, $subject, $message, implode("\r\n", $headers));
            
            if($result) 
                return true;
            return false;
        }

        public function question_correction_mail($to, $question)
        {
            $template = R::findOne( 'email_template', ' title = ? ', [ 'question_correction' ] );
            $subject = $template->subject;
            $message = $template->template;

            $message = str_replace("#question", $question->question, $message);
            $message = str_replace("#option1", $question->option_one, $message);
            $message = str_replace("#option2", $question->option_two, $message);
            $message = str_replace("#option3", $question->option_three, $message);
            $message = str_replace("#option4", $question->option_four, $message);
            $message = str_replace("#ans", $question->ans, $message);
            if(!empty($question->question_img)){
                $message = str_replace("#qImg", '<img src="https://mcqschools.com/'.$question->question_img.'" style="max-width: 300px;" alt="">', $message);
            }else{
                $message = str_replace("#qImg", " ", $message );
            }

            // To send HTML mail, the Content-type header must be set
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
            $headers[] = 'From: mcqschools <noreply@mcqschools.com>';
            //$headers[] = 'From: mcqschools <info@mcqschools.com>';
        
            $result =  mail($to, $subject, $message, implode("\r\n", $headers));
            
            if($result) 
                return true;
            return false;
        }

    }

?>