<?php

    include 'repository/subscriptionRepository.php';


    class SubscriptionController
    {
        private $repository;

        function __construct()
        {
            $this->repository = new SubscriptionRepository();
        }

        public function subscribe($param)
        {
            $this->repository->subscribe($param[0]);
            if( strpos( $_SERVER['HTTP_REFERER'], 'localhost' ) == true ){
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit;
            }
            header("Location: /");
            exit;
        }

        public function unsubscribe($param)
        {
            $this->repository->unsubscribe($param[0]);
            if( strpos( $_SERVER['HTTP_REFERER'], 'localhost' ) == true ){
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit;
            }
            header("Location: /");
            exit;
        }

        public function subscription_list()
        {
            $list = $this->repository->subscription_list();
            return $list;
        }

    }

?>