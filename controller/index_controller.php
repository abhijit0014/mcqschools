<?php

    include 'repository/questionRepository.php';
    include 'repository/historyRepository.php';

    class IndexController
    {
        private $repository;
        private $historyRepository;

        function __construct()
        {
            $this->repository = new QuestionRepository();
            $this->historyRepository = new HistoryRepository();
        }

        public function index()
        {
            $day = $this->historyRepository->getImportantDay();
            $view = new view('index');
            $view->assign('today', $day);
            $view->assign('current_date', date('Y-m-d H:i:s'));
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