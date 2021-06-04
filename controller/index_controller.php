<?php

    include 'repository/questionRepository.php';


    class IndexController
    {
        private $repository;

        function __construct()
        {
            $this->repository = new QuestionRepository();
        }

        public function index()
        {
            $view = new view('index');
            $view->assign('category_name', null);
            return;
        }

        public function privacy_policy()
        {
            $view = new view('privacy_policy');
            return;
        }

        public function test()
        {
            $view = new view('test_index');
            return;
        }

    }

?>