<?php

    include 'repository/examRepository.php';
    include 'repository/questionRepository.php';
    include 'repository/categoryRepository.php';
    include 'repository/examUserRepository.php';
    include 'repository/examResultRepository.php';
    include 'repository/userRepository.php';
    include 'repository/subscriptionRepository.php';
    //include 'model/questionResponseModel.php';


    class ExamcenterController
    {
        private $questionRepository;
        private $examRepository;
        private $categoryRepository;
        private $examUserRepository;
        private $examResultRepository;
        private $userRepository;
        private $subscriptionRepository;

        function __construct()
        {
            $this->questionRepository = new QuestionRepository();
            $this->examRepository = new ExamRepository();
            $this->categoryRepository = new CategoryRepository();
            $this->examUserRepository = new ExamUserRepository();
            $this->examResultRepository = new ExamResultRepository();
            $this->userRepository = new UserRepository();
            $this->subscriptionRepository = new SubscriptionRepository();
        }

        public function live($param)
        {
            $exam =  $this->examRepository->getOne(289);
            $view = new view('live');
            $view->assign('exam',  $exam);
            date_default_timezone_set('Asia/Kolkata');
            $view->assign('current_time',  date('Y-m-d H:i:s'));
            $d=mktime(10, 00, 00, 6, 27, 2021);
            // $d=mktime(10, 23, 00, 6, 6, 2021);
            $view->assign('start_time',   date("Y-m-d H:i:s", $d));

            $toppers = $this->examUserRepository->getRank(289);
            $view->assign('toppers', $toppers);
            return;
        }

        public function start($param)
        {
            $exam =  $this->examRepository->getOne($param[0]);
            $view = new view('examcenter_start');
            $view->assign('exam',  $exam);
            return;
        }

        public function exam($param)
        {
            $examId = $param[0];
            $userId = SessionManager::get("user_id");

            $exam =  $this->examRepository->getOne($examId);
            if(!$exam->published){
                header("Location: /examcenter/start/".$examId); exit;
            }

            $examUser = $this->examUserRepository->getByExamIdAndUserId($examId,$userId);
            if(!$examUser){
                $examUser = $this->examUserRepository->create($examId,$userId);
                $exam->attemped++;
                R::store($exam);
            }else if($examUser->submitted){
                header("Location: /examcenter/result/".$examId); exit;
            }
            
            $examResult = $this->examResultRepository->getResult($examUser->id);
            $questionlist = $this->questionRepository->getByExamId($examId);
            SessionManager::set("currentExamUserId",  $examUser->id);
            
            $view = new view('examcenter_exam');
            $view->assign('exam',  $exam);
            $view->assign('questionlist',  json_encode($questionlist));
            $view->assign('examUser',  $examUser);
            $view->assign('examResult',  json_encode($examResult));
            return;
        }

        public function saveResult($param)
        {
            $exam_user_id = $param[0];
            $question_id = $param[1];
            $ansOption = $param[2];
            if(SessionManager::get("currentExamUserId")==$exam_user_id)
                $exam =  $this->examResultRepository->save($exam_user_id, $question_id, $ansOption);

            return;
        }

        public function deleteResult($param)
        {
            $exam_user_id = $param[0];
            $question_id = $param[1];
            if(SessionManager::get("currentExamUserId")==$exam_user_id)
                $exam =  $this->examResultRepository->delete($exam_user_id, $question_id);

            return;
        }

        public function submit($param)
        {
            $exam_user_id = $param[0];
            $duration = $param[1];
            if(SessionManager::get("currentExamUserId")==$exam_user_id)
                $exam =  $this->examUserRepository->submit($exam_user_id, $duration);

            return;
        }

        public function updateDuration($param)
        {
            $exam_user_id = $param[0];
            $duration = $param[1];
            $pause = false;

            if(isset($param[2]))
                $pause = true;
            
            if(SessionManager::get("currentExamUserId")==$exam_user_id)
                $exam =  $this->examUserRepository->updateDuration($exam_user_id, $duration, $pause);
            return;
        }


        public function result($param)
        {
            $examId = $param[0];
            //$examId = $param[0]; regenerate result flag
            $userId = SessionManager::get("user_id");

            $examUser = $this->examUserRepository->getByExamIdAndUserId($examId,$userId);
            if(!$examUser->submitted){
                header("Location: /examcenter/exam/".$examId); exit;
            }
            
            $exam =  $this->examRepository->getOne($examId);
            $examResultList = $this->examResultRepository->getResult($examUser->id);
            $questionlist = $this->questionRepository->getByExamId($examId);

            if (!$examUser->correct_answered && $examUser->submitted) 
            {
                $correctAns = 0;
                $wrongAns = 0;
                $notAnswered = 0;
                
                foreach ($examResultList  as $result) {
                    foreach ($questionlist as $question){
                        if($result['question_id'] == $question['id']){
                            $result['ans'] = ($result['ans_option'] == $question['ans']) ? "true" : "false";
                            R::exec("UPDATE exam_result SET ans = ".$result['ans']." WHERE id = ".$result['id']);
                        }
                    }
                }
                
                $correctAns = R::count('exam_result', 'ans = true AND exam_user_id =  ?', [$examUser->id]);
                $wrongAns = R::count('exam_result', 'ans = false AND exam_user_id =  ?', [$examUser->id]);
                $notAnswered = sizeof($questionlist) - $correctAns - $wrongAns;
                
                $obtainedMarks = $correctAns*$exam->point - $wrongAns*$exam->negative_point;
                
                $examUser->correct_answered = $correctAns;// correct ans
                $examUser->not_answered = $notAnswered;// wrong ans
                $examUser->wrong_answered = $wrongAns; // #not answered
                $examUser->total_marks = sizeof($questionlist) * $exam->point; //total marks
                $examUser->obtained_marks = $obtainedMarks; // obtained marks
                $examUser->duration = $exam->duration_mins - $examUser->duration;
                $examUser->rank_score = ($correctAns/sizeof($questionlist)) * 100;
                R::store( $examUser);

                // refresh eaxm result list
                $examResultList = $this->examResultRepository->getResult($examUser->id);
            }

            // prepare result list
            $questionResultlist = [];
            foreach ($questionlist as $question) {
                $flag = false;
                foreach ($examResultList  as $result) {
                    if($question['id']==$result['question_id']) {
                        $question['user_result']=$result['ans'];
                        $question['user_ans']=$result['ans_option'];
                        $flag = true; break;
                    }
                }
                if(!$flag){
                    $question['user_result']=null;
                    $question['user_ans']=null;
                }
                array_push($questionResultlist, $question);
            }


            $creator = $this->userRepository->getById($exam->created_by);
            //$creator_profile = $this->userRepository->getByProfileId($exam->created_by);
            //$creator_name = empty($creator->display_name)? $creator->username : $creator->display_name;

            $toppers = $this->examUserRepository->getToppers($examId);
            

            $view = new view('examcenter_result');
            $view->assign('exam',  $exam);
            $view->assign('questionlist',  $questionResultlist);
            $view->assign('examUser',  $examUser);
            $view->assign('creator_username', $creator->username);
            $view->assign('toppers',  $toppers);
            return;
        }

        public function myresults($param)
        {
            $user_id = SessionManager::get("user_id");
            $limit = 10;
            $pages = null;
            $currentPage = null;

            $currentPage = isset($param[0]) ? $param[0] : 1;
            
            if(!isset($param[1]))
                $pages = $this->examUserRepository->pageCount($limit, $user_id);
            else 
                $pages = $param[1];

            $list = $this->examUserRepository->list($currentPage, $limit, $user_id);

            $view = new view('examcenter_result_list');
            $view->assign('examlist', $list);
            $view->assign('currentPage', $currentPage);
            $view->assign('pages', $pages);
            return;
        }



    }

?>