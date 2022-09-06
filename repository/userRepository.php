<?php

    class UserRepository
    {
        function __construct()
        {

        }

        public function getById($id)
        {
            return R::load( 'users', $id );
        }
        
        public function getByEmail($email){
            return R::findOne( 'users', ' email = ? ', [ $email ] );
        }

        public function getByUsername($username){
            return R::findOne( 'users', ' username = ? ', [ $username ] );
        }

        public function getProfileById($user_id)
        {
            return R::findOne( 'users_info', ' user_id = ? ', [ $user_id ] ); 
        }

        
        // user registration
        public function register($obj)
        {
            $user = R::findOne( 'users', ' email = ? ', [ $obj['email'] ] );
            if(empty($user))
            {
                // user name
                $username = strstr( $obj['email'], '@', true);
                $flag = true;
                while($flag){
                    $countUsers  = R::count('users', 'username = ?', [$username]);
                    if(empty($countUsers)) break;
                    $username = $username.$countUsers;
                }

                // user table
                $user = R::dispense( 'users' );
                $user->email = $obj['email'];
                $user->enabled = true;
                $user->username = $username;
                $user->pass = password_hash($obj['pass'], PASSWORD_DEFAULT);
                $user->reg_date = date('Y-m-d H:i:s');
                $id = R::store( $user );

                // user_info table
                $user_info = R::xdispense( 'users_info' );
                $user_info->user_id = $id;
                $user_info->location = $obj['address'];
                R::store( $user_info );
                return true;
            }
            return false;
        }

        // user login
        public function login($obj)
        {
            $user = R::findOne( 'users', ' email = ? ', [ $obj['email'] ] );
            if(!empty($user))
            {
                $result =  password_verify($obj['pass'], $user->pass);
                if($result){
                    $user->last_login = date('Y-m-d H:i:s');
                    R::store( $user );
                    return $user;
                }
            }
            return null;
        }

        
        // update profile
        public function updateProfile($arr){
            $user_id = SessionManager::get("user_id");
            $user_info = R::findOne( 'users_info', ' user_id = ? ', [ $user_id ] ); 
            if(empty($user_info)){
                $user_info = R::xdispense( 'users_info' );
                $user_info->user_id = $user_id;
            }
            $user_info->display_name = $arr['displayName'];
            $user_info->location = $arr['location'];
            $user_info->about_me = $arr['aboutMe'];
            return R::store( $user_info );
        }

        // change username
        public function updateUsername($arr){
            $user = $this->getByUsername($arr['username']);
            if(empty($user)){
                $user = R::load( 'users', SessionManager::get("user_id") );
                $user->username = preg_replace('/[^A-Za-z0-9\-]/', '', $arr['username']);
                R::store( $user );
                return true;
            }
            return false;
        }

        // change email
        public function updateEmail($arr){
            $user = $this->getByEmail($arr['email']);
            if(empty($user)){
                $user = R::load( 'users', SessionManager::get("user_id") );
                $user->email = $arr['email'];
                $result =  password_verify($arr['pass'], $user->pass);
                if($result){
                    R::store( $user );
                    return true;
                }
            }
            return false;
        }

        // password change
        public function updatePassword($arr){
            $user = R::load( 'users', SessionManager::get("user_id") );
            $result =  password_verify($arr['oldPass'], $user->pass);

            if($result){
                $user->pass = password_hash($arr['newPass'], PASSWORD_DEFAULT);
                $user->last_pass_change = date('Y-m-d H:i:s');
                R::store( $user );
                return true;
            }
            return false;
        }


        // user status ---------------------------------------------------
        public function status($userId)
        {
            $status = R::getRow("SELECT username, exam_user.user_id, location, count(exam_id) as test_count, 
            sum(correct_answered) as correct_ans_count, 
            sum(correct_answered+wrong_answered) as question_count,
            round(sum(obtained_marks)) as obtained_marks
            FROM exam_user  
            left join users on users.id = exam_user.user_id 
            left join users_info on users.id = users_info.user_id 
            WHERE exam_user.user_id = :userId", array(':userId'=>$userId) );
            return $status;
        } 

    }

?>