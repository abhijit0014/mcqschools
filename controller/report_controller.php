<?php

    include 'repository/reportRepository.php';
    include 'repository/examRepository.php';
    include 'repository/questionRepository.php';


    class ReportController
    {
        private $reportRepository;
        private $questionRepository;
        private $examRepository;

        function __construct()
        {
            $this->reportRepository = new ReportRepository();
            $this->questionRepository = new QuestionRepository();
            $this->examRepository = new ExamRepository();
        }

        public function save()
        {
            if(isset($_POST))
                $id = $this->reportRepository->save($_POST);
        }

        public function list($param) // report list for creator
        {
            $view = new view('report_list');
            return;
        }

        // return json reports
        public function listByCreatorId($param) 
        {
            $creator_id = SessionManager::get("user_id");;
            $limit = 10;
            $currentPage = null;
            $currentPage = isset($param[0]) ? $param[0] : 1;
            $list = $this->reportRepository->reportByCreatorId($creator_id, $currentPage, $limit);
            return json_encode($list);
        }

        public function solved($param) 
        {
            $id = $param[0];
            $this->reportRepository->solved($id);
            return true;
        }

        public function examReview($param) // report list for creator
        {
            $exam_id = $param[0];
            $exam = $this->examRepository->getOne($exam_id);
            $reports = $this->reportRepository->examReportsByIdAndUnsolved($exam_id);
            $view = new view('review_exam');
            $view->assign('exam', $exam);
            $view->assign('reportList', $reports);
            return;
        }

        public function reviewRequest($param){
            $request_type = $param[0];
            $id = $param[1];
            if($request_type=="exam"){
                $this->reportRepository->examReviewRequest($id);
            }else{
                $this->reportRepository->questionReviewRequest($id);
            }
            return true;
        }

    }

?>