<?php

    include 'repository/examRepository.php';
    include 'repository/userRepository.php';
    include 'repository/subscriptionRepository.php';


    class CreatorController
    {
        private $examRepository;
        private $userRepository;
        private $subscriptionRepository;

        function __construct()
        {
            $this->examRepository = new ExamRepository();
            $this->userRepository = new UserRepository();
            $this->subscriptionRepository = new SubscriptionRepository();
        }

        public function info($param)
        {
            $username = $param[0];
            $creator = $this->userRepository->getByUsername($username);
            $creator_profile = $this->userRepository->getProfileById($creator->id);
            $creator_profile->display_name = empty($creator_profile->display_name) ? $creator->username : $creator_profile->display_name;

            $isSubscribed = $this->subscriptionRepository->check_subscription($creator->id);


            $view = new view('creator');
            $view->assign('isSubscribed', $isSubscribed);
            $view->assign('creator', $creator_profile );
            return;
        }

        public function exam_list_api($param)
        {
            $limit = 10;
            $creator_id = null;
            $currentPage = null;
            $creator_id = $param[0];
            $currentPage = isset($param[1]) ? $param[1] : 1;
            $list = $this->examRepository->publishedExamList($currentPage, $limit, $creator_id);
            return json_encode($list);
        }



    }

?>