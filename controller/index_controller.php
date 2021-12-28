<?php

    include 'repository/questionRepository.php';
    include 'repository/examRepository.php';
    include 'repository/historyRepository.php';
    include 'repository/examUserRepository.php';
    include 'repository/webstatusRepository.php';
    include 'repository/propertyRepository.php';

    class IndexController
    {
        private $repository;
        private $examRepository;
        private $historyRepository;
        private $examUserRepository;
        private $webstatusRepository;
        private $propertyRepository;

        function __construct()
        {
            $this->repository = new QuestionRepository();
            $this->historyRepository = new HistoryRepository();
            $this->examUserRepository = new ExamUserRepository();
            $this->webstatusRepository = new WebStatusRepository();
            $this->examRepository = new ExamRepository();
            $this->propertyRepository = new PropertyRepository();
        }

        public function index()
        {
            $day = $this->historyRepository->getImportantDay();
            $rankList =  $this->examUserRepository->monthlyTestRank(date("Y-m-d H:i:s"),10);
            $status = $this->webstatusRepository->get_status();
            $liveQuiz =  $this->examRepository->getLiveQuiz();
            $suggestedExams =  $this->examRepository->listOfSuggestedExams();
            $property = $this->propertyRepository->get();
            $liveExam = $this->examRepository->getOne($property->live_exam_id);

            $view = new view('index_new');
            $view->assign('today', $day);
            $view->assign('current_date', date('Y-m-d H:i:s'));
            $view->assign('rankList', $rankList);
            $view->assign('status', $status);
            $view->assign('liveExam', $liveExam);
            $view->assign('liveQuiz', $liveQuiz);
            $view->assign('suggestedExams', $suggestedExams);
            $view->assign('property', $property);
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