<?php
    
    include 'repository/userRepository.php';
    include 'repository/otpRepository.php';
    include 'service/emailService.php';
    //include 'repository/tokenRepository.php';


    class UserController
    {
        private $repository;
        private $emailService;
        private $otpRepository;
        private $tokenRepository;

        function __construct()
        {
            $this->repository = new UserRepository();
            $this->otpRepository = new OtpRepository();
            $this->emailService = new EmailService();
            $this->tokenRepository = new TokenRepository();
        }

        public function login($param)
        {
            $redirect_url = '/';
            if (isset($_SERVER['HTTP_REFERER'])){

                //SessionManager::set("redirect_url", $redirect_url);

                $redirect_url = $_SERVER['HTTP_REFERER'];
                if( strpos( $redirect_url, "/user/register" ) !== false )
                    $redirect_url = "/";
                if( strpos( $redirect_url, "/user/reset_password" ) !== false )
                    $redirect_url = "/";
                if( strpos( $redirect_url, "/user/login" ) !== false )
                    $redirect_url = "/";
            }

            $error = isset($param[0]) ? true : false;
            $view = new view('user_login');
            $view->assign('redirect', $redirect_url);
            $view->assign('error', $error);
            return;
        }

        public function register($param)
        {
            $error = isset($param[0]) ? true : false;
            $view = new view('user_register');
            $view->assign('error', $error);
            return;
        }

        public function logout()
        {
            SessionManager::clear();
            setcookie("jwt", "", time() - 3600, "/");
            header("Location: /");
        }



        public function login_process()
        {
            if(isset($_POST))
            {
                if(!isset( $_POST['email']) || !isset( $_POST['pass'])){
                    header("Location: /user/login/error"); exit;
                }

                $principle = $this->repository->login($_POST);
                if(!empty($principle))
                {
                    SessionManager::set("authenticated", true);
                    SessionManager::set("email", $principle->email);
                    SessionManager::set("user_id", $principle->id);
                    SessionManager::set("user_role", $principle->user_role);

                    $this->tokenRepository->createToken($principle);
                    header("Location: ".$_POST['redirect']); exit;
                }
                else{
                    header("Location: /user/login/error"); exit;
                }
            }
        }

        public function registration_process()
        {
            if(isset($_POST)){
                $result = $this->repository->register($_POST);
                if($result){
                    header("Location: /user/login"); exit;
                }else{
                    header("Location: /user/register/error"); exit;
                }
            }
        }


        // profile setting---------------------------------------------------

        // profile
        public function profile($param)
        {
            $user_id = SessionManager::get("user_id");
            $user_info =  $this->repository->getProfileById($user_id);
            $view = new view('user_edit_profile');
            $view->assign('user', $user_info);
            return;
        }

        public function update_profile()
        {
            $this->repository->updateProfile($_POST);
            header("Location: /user/profile?error=false&msg=Profile updated successfully");
            exit;
        }


        // username
        public function username()
        {
            $user_id = SessionManager::get("user_id");
            $user =  $this->repository->getById($user_id);
            $view = new view('user_edit_username');
            $view->assign('username', $user->username);
            return;
        }

        public function check_username($param){
            $result =  $this->repository->getByUsername($param[0]);
            if(empty($result)) return true;
            else return false;
        }

        public function update_username()
        {
            $result = $this->repository->updateUsername($_POST);
            if($result){
                header("Location: /user/username?error=false&msg=Username updated successfully"); exit;
            }else{
                header("Location: /user/username?error=true&msg=Username already exists. Try another"); exit;
            }
        }

        // email
        public function email()
        {
            $user_id = SessionManager::get("user_id");
            $user =  $this->repository->getById($user_id);
            $view = new view('user_edit_email');
            $view->assign('email', $user->email);
            return;
        }

        public function check_email($param){
            $result =  $this->repository->getByEmail($param[0]);
            if(empty($result)) return true;
            else return false;
        }

        public function update_email()
        {
            $result = $this->repository->updateEmail($_POST);
            if($result){
                header("Location: /user/email?error=false&msg=Email updated successfully"); exit;
            }else{
                header("Location: /user/email?error=true&msg=Enter a valid password"); exit;
            }
        }

        // password
        public function password()
        {
            $view = new view('user_edit_password');
            return;
        }

        public function update_password()
        {
            $result = $this->repository->updatePassword($_POST);
            if($result){
                header("Location: /user/password?error=false&msg=Password updated successfully"); exit;
            }else{
                header("Location: /user/password?error=true&msg=Enter a valid password"); exit;
            }
        }



        // forgot password ----------------------------------------------------
        public function reset_password()
        {
            $view = new view('user_forgot_password');
            return;
        }

        public function reset_password_process(){
            $email = $_POST['email'];
            $newPass = $_POST['newPass'];
            $cnfNewPass = $_POST['cnfNewPass'];
            $otp = $_POST['otp'];

            if($newPass!=$cnfNewPass)
                header("Location: /user/reset_password");

            $otp = $this->otpRepository->findOtp($otp, $email);
            if(!empty($otp)){
                R::trash('otp',$otp->id);
                $user =  $this->repository->getByEmail($email);
                $user->pass = password_hash($newPass, PASSWORD_DEFAULT);
                $user->last_pass_change = date('Y-m-d H:i:s');
                R::store( $user );
                header("Location: /user/login"); exit;
            }
            header("Location: /user/reset_password"); exit;
        }

        public function verifyOtp($param)
        {
            $email = $param[0];
            $otp = $param[1];
            $otp = $this->otpRepository->findOtp($otp, $email);
            if(!empty($otp)){
                $current_time = date('Y-m-d H:i:s');
                $end_time = date( 'Y-m-d H:i:s', strtotime($otp->end_time) );
                if($current_time < $end_time)
                    return "success";
                else
                    return "error?Otp expired. Please try again";
            }
            return "error?Entered a wrong otp";
        }

        public function sendOtp($param)
        {
            if(isset($param[0])){
                $email = $param[0];
                $user =  $this->repository->getByEmail($email);
                if(!empty($user)){
                    $otp = $this->otpRepository->create_otp($email);
                    $flag = $this->emailService->send_password_reset_mail($email, $otp);
                }
            }
            return true;
        }

    }

?>