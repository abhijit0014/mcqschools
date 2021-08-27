<?php

    include 'repository/questionRepository.php';
    include 'repository/examRepository.php';
    include 'repository/historyRepository.php';
    include 'repository/examUserRepository.php';
    include 'repository/webstatusRepository.php';

    class IndexController
    {
        private $repository;
        private $examRepository;
        private $historyRepository;
        private $examUserRepository;
        private $webstatusRepository;

        function __construct()
        {
            $this->repository = new QuestionRepository();
            $this->historyRepository = new HistoryRepository();
            $this->examUserRepository = new ExamUserRepository();
            $this->webstatusRepository = new WebStatusRepository();
            $this->examRepository = new ExamRepository();
        }

        public function index()
        {
            $day = $this->historyRepository->getImportantDay();
            $rankList =  $this->examUserRepository->monthlyTestRank(date("Y-m-d H:i:s"),10);
            $status = $this->webstatusRepository->get_status();
            $liveExam = $this->examRepository->getOne($GLOBALS['LIVE_EXAM_ID']);
            $liveQuiz =  $this->examRepository->getLiveQuiz();

            $view = new view('index_new');
            $view->assign('today', $day);
            $view->assign('current_date', date('Y-m-d H:i:s'));
            $view->assign('rankList', $rankList);
            $view->assign('status', $status);
            $view->assign('liveExam', $liveExam);
            $view->assign('liveQuiz', $liveQuiz);
            return;
        }

        public function privacy_policy()
        {
            $view = new view('privacy_policy');
            return;
        }

        public function test()
        {
            $view = new view('menu_test');
            return;
        }

    }

?>