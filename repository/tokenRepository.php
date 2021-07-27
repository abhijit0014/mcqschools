<?php

    class TokenRepository
    {
        function __construct()
        {

        }

        public function createToken($user)
        {
            $login_details = R::findOne( 'login_details', ' user_id = ? ', [ $user->id] );
            if(empty($login_details)){
                $login_details = R::xdispense( 'login_details' );
            }
            
            $login_details->user_id = $user->id;
            $login_details->browser = $_SERVER['HTTP_USER_AGENT']; ;
            $login_details->created_date = date('Y-m-d H:i:s');
            $login_details->jwt = null;

            while(!$login_details->jwt){
                $temp = uniqid();
                $details = R::findOne( 'login_details', ' jwt = ? ', [ $temp ] );
                if(empty($details)) $login_details->jwt = $temp;
            }
            
            R::store($login_details);

            setcookie('jwt', $login_details->jwt, time() + (86400 * 15), "/");
            return  $login_details->jwt;
        }

        public function updateToken($login_details)
        {
            $login_details->jwt = null;
            while(!$login_details->jwt){
                $temp = uniqid();
                $details = R::findOne( 'login_details', ' jwt = ? ', [ $temp ] );
                if(empty($details)) $login_details->jwt = $temp;
            }
            $login_details->created_date = date('Y-m-d H:i:s');
            R::store($login_details);

            setcookie('jwt', $login_details->jwt, time() + (86400 * 15), "/");
            return $login_details->jwt;
        }       
    }

?>