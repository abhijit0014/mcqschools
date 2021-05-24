<?php

    class OtpRepository
    {
        function __construct()
        {

        }

        public function create_otp($email)
        {
            //$user_id = SessionManager::get("user_id");
            $string = '01234567899876543210';
            $string_shuffled = str_shuffle($string);
            $otp = substr($string_shuffled, 1, 6);
            $expiry_time = date('Y-m-d H:i:s', time()+ (15 * 60));

            $otpObject = R::findOne( 'otp', ' email = ? ', [ $email ] );
            if(empty($otpObject)){
                $otpObject = R::dispense( 'otp' );
                $otpObject->email = $email;
            }

            $otpObject->otp = $otp;
            $otpObject->end_time = $expiry_time;
            //$otpObject->user_id = $user_id;
            
            R::store( $otpObject );
            return $otp;
        }

        public function findOtp($otp, $email)
        {
            return R::findOne( 'otp', ' otp = ? AND email = ? ', [ $otp, $email ] );
        }

    }

?>