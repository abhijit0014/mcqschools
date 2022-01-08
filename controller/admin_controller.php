<?php

   // include 'repository/examRepository.php';
   // include 'repository/questionRepository.php';
    //include 'repository/categoryRepository.php';
   // include 'repository/userRepository.php';
    include 'repository/reportRepository.php';
    include 'repository/propertyRepository.php';


    class AdminController
    {
        //private $questionRepository;
        //private $examRepository;
        //private $categoryRepository;
        //private $userRepository;
        private $reportRepository;
        private $propertyRepository;

        function __construct()
        {
            //$this->questionRepository = new QuestionRepository();
            //$this->examRepository = new ExamRepository();
            //$this->categoryRepository = new CategoryRepository();
            //$this->userRepository = new UserRepository();
            $this->reportRepository = new ReportRepository();
            $this->propertyRepository = new PropertyRepository();
        }

        public function admin($param)
        {
            header("Location: /admin/questionReportList");
            exit;
        }

        // smart edit ----------------------------------------------
        public function smart_edit()
        {
            $view = new view('admin/smart_edit');
            $view->assign('email', SessionManager::get("email"));
            return;
        }

        // exam report ---------------------------------------------
        public function examReportList($param)
        {
            $view = new view('admin/report_exam');
            $list = $this->reportRepository->examReportList();
            $view->assign('reportList', $list);
            return;
        }

        public function examReportApi($param) // Json
        {
            $examId = $param[0];
            $list = $this->reportRepository->examReportsById($examId);
            return  json_encode($list);
        }

        public function block_exam($param){
            $examId = $param[0];
            $this->reportRepository->blockExam($examId);
            return true;
        }

        // question report -----------------------------------------------
        public function questionReportList($param)
        {
            $view = new view('admin/report_question');
            $list = $this->reportRepository->questionReportList();
            $view->assign('reportList', $list);
            return;
        }

        public function questionReportApi($param) // Json
        {
            $questionId = $param[0];
            $list = $this->reportRepository->questionReportsById($questionId);
            return  json_encode($list);
        }

        public function block_question($param){
            $questionId = $param[0];
            $this->reportRepository->blockQuestion($questionId);
            return true;
        }

        // property update ----------------------------------------------------
        public function property()
        {
            $view = new view('admin/property_form');
            $view->assign('property', $this->propertyRepository->get());
            return;
        }
        public function property_update()
        {
            if(isset($_POST))
                $this->propertyRepository->save($_POST);
                
            header("Location: /admin/property"); exit;
        }
    }