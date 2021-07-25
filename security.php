<?php

    include 'repository/tokenRepository.php';

    class SecurityManager
    {
        public $securedEndpoints;
        private $tokenRepository;

        function __construct()
        {
            $this->securedEndpoints = json_decode(file_get_contents('endpoints.json'));
            $this->tokenRepository = new TokenRepository();
        }

        // auto login
        public function autoLogin()
        {
            $authenticated = SessionManager::get('authenticated');
            if (empty($authenticated) && isset($_COOKIE['jwt']))
            {
                $jwt =  $_COOKIE['jwt'];
                $login_details = null;
                $user = null;

                if(!empty($jwt)){
                    $login_details = R::findOne( 'login_details', ' jwt = ? ', [ $jwt ] );
                }

                if(!empty($login_details)){
                    $browser = $_SERVER['HTTP_USER_AGENT'];
                    if(strcmp($browser,$login_details->browser)==0){
                        $user = R::load( 'users', $login_details->user_id);
                    }
                }

                if($user){
                    SessionManager::set("authenticated", true);
                    SessionManager::set("email", $user->email);
                    SessionManager::set("user_id", $user->id);
                    SessionManager::set("user_role", $user->user_role);

                    $this->tokenRepository->updateToken($login_details);
                }
            }
        }

        // authenticate user
        public function authenticate($url)
        { 
            $isSecured = false;
            $required_roles = null; 
            $csrf_protected = false; 
            
            foreach ($this->securedEndpoints as $endpoint) {
                if (strpos($url, $endpoint->url) !== false) {
                    $isSecured = true;
                    $required_roles = $endpoint->role; 
                    $csrf_protected = $endpoint->csrf; 
                    break;
                }
            }

            
            if ($isSecured){

                // check login
                $result = SessionManager::get('authenticated');
                if (empty($result)) {
                    header("Location: /user/login");
                    exit;
                }

                // check role
                $user_role = SessionManager::get('user_role');
                if (!in_array($user_role, $required_roles)){
                    header("Location: /");
                    exit;
                }

                //csrf
                if ($csrf_protected)
                {
                    if(!isset($_SERVER['HTTP_REFERER'])){
                        header("Location: /"); exit;
                    }

                    if( strpos( $_SERVER['HTTP_REFERER'], 'mcqschools.com' ) == false ){
                        header("Location: /"); exit;
                    }
                }

            }


        }
    }

?>