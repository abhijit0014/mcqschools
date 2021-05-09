<?php

    class SecurityManager
    {
        public $securedEndpoints;

        function __construct()
        {
            $this->securedEndpoints = json_decode(file_get_contents('endpoints.json'));
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

                    if( strpos( $_SERVER['HTTP_REFERER'], 'localhost' ) == false ){
                        header("Location: /"); exit;
                    }
                }

            }


        }
    }

?>