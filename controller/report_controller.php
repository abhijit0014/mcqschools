<?php

    include 'repository/reportRepository.php';
    include 'repository/examRepository.php';
    include 'repository/questionRepository.php';
    include 'service/emailService.php';
    include 'repository/userRepository.php';


    class ReportController
    {
        private $reportRepository;
        private $questionRepository;
        private $examRepository;
        private $emailService;
        private $userRepository;

        function __construct()
        {
            $this->reportRepository = new ReportRepository();
            $this->questionRepository = new QuestionRepository();
            $this->examRepository = new ExamRepository();
            $this->emailService = new EmailService();
            $this->userRepository = new UserRepository();
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
            $report = $this->reportRepository->solved($id);
            if(!empty($report->question_id)){
                $question = $this->questionRepository->getOne($report->question_id);
                $student =  $this->userRepository->getById($report->created_by); 
                
                if(!empty($question->updated_date)){
                    $diff=date_diff(date_create($question->updated_date),date_create())->format("%a");
                    if($diff==0)
                        $this->emailService->question_correction_mail($student->email, $question);
                }
            }
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