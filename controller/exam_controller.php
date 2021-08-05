<?php

    include 'repository/userRepository.php';
    include 'repository/examRepository.php';
    include 'repository/examUserRepository.php';
    include 'repository/questionRepository.php';
    include 'repository/categoryRepository.php';
    include 'repository/subscriptionRepository.php';


    class ExamController
    {
        private $repository;
        private $categoryRepository;
        private $examUserRepository;
        private $questionRepository;
        private $userRepository;
        private $subscriptionRepository;

        function __construct()
        {
            $this->repository = new ExamRepository();
            $this->examUserRepository = new ExamUserRepository();
            $this->categoryRepository = new CategoryRepository();
            $this->questionRepository = new QuestionRepository();
            $this->userRepository = new UserRepository();
            $this->subscriptionRepository = new SubscriptionRepository();
        }

        public function add()
        {
            $exam = R::dispense( 'exam' );
            $exam->point = 1;
            $exam->negative_point = 0;
            $exam->duration_mins = 10;

            $view = new view('exam_form');
            $view->assign('exam',  $exam );
            $view->assign('category', R::dispense( 'category' ));
            return;
        }

        public function edit($param)
        {
            if(isset($param[0]))
            $exam =  $this->repository->getOne($param[0]);
            $category =  $this->categoryRepository->getOne($exam->category_id);

            if($exam->start_time)
            $exam->start_time = date('Y-m-d\TH:i', strtotime($exam->start_time));
            if($exam->end_time)
            $exam->end_time = date('Y-m-d\TH:i', strtotime($exam->end_time));

            $view = new view('exam_form');
            $view->assign('exam', $exam);
            $view->assign('category', $category);
            return;
        }

        public function save()
        {
            if(isset($_POST))
            {
                if(!is_numeric($_POST['point']) || !is_numeric($_POST['negative_point'])){
                    header("Location: /exam/list"); exit;
                }

                if(!$this->repository->checkAllowedExamLimit()){
                    header("Location: /exam/list?error=true&msg=You can add maximum ". $GLOBALS['EXAM_ADITION_LIMIT_PER_DAY']." exam per day"); exit;
                }

                $id = $this->repository->save($_POST);
                header("Location: /exam/list"); exit;
            }
        }

        public function publish($param)
        {
            if(isset($param[0])){
                $exam =  $this->repository->getOne($param[0]);
                $exam->number_of_question = $this->questionRepository->questionCountByExamId($exam->id);

                if(SessionManager::get("user_id") == $exam->created_by){
                    if($exam->number_of_question >= 10){
                        $exam->published = $exam->published ? false : true;
                        $exam->created_date = $exam->attemped == 0 ? date('Y-m-d H:i:s') : $exam->created_date;
                        $user_info = $this->userRepository->getProfileById($exam->created_by);
                        $user_info->exam_count = $this->repository->countExamByCreatorId($exam->created_by); 
                        R::store( $user_info );
                        R::store( $exam );
                    }else{
                        R::store( $exam ); 
                        header("Location: ".$_SERVER['HTTP_REFERER']."?error=true&msg=Minimum 10 questions required to publish a exam");
                        exit;
                    }
                }
            }

            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit;
        }

        public function list($param)
        {
            $user_id = SessionManager::get("user_id");
            $limit = 10;
            $pages = null;
            $currentPage = null;

            $currentPage = isset($param[0]) ? $param[0] : 1;
            
            if(!isset($param[1]))
                $pages = $this->repository->pageCount($limit, $user_id);
            else 
                $pages = $param[1];

            $list = $this->repository->list($currentPage, $limit, $user_id);
            $creator = $this->userRepository->getById($user_id);

            $view = new view('exam_list');
            $view->assign('examlist', $list);
            $view->assign('currentPage', $currentPage);
            $view->assign('pages', $pages);
            $view->assign('user_id', $user_id);
            $view->assign('creator', $creator->username);
            return;
        }

        public function delete($param)
        {
            $examId = $param[0];
            $exam =  $this->repository->getOne($examId);

            if($exam->created_by == SessionManager::get("user_id"))
                $this->repository->delete($examId);

            header("Location: /exam/list");
            exit;
        }

        // subscription 
        public function subscription(){
            $subscriptionList = $this->subscriptionRepository->subscription_list();
            $view = new view('exam_subscribed_list');
            $view->assign('subscriptionList', $subscriptionList);
        }

        public function subscription_result_api($param){
            $limit = 10;
            $user_id = SessionManager::get("user_id");
            $currentPage = isset($param[0]) ? $param[0] : 1;

            $list = $this->repository->listBySubscription($user_id, $currentPage, $limit);
            return json_encode($list);
        }

        public function suggestedExamsApi()
        {
            $json = file_get_contents('php://input');
            $arr = json_decode($json);
            $list = $this->repository->listOfSuggestedExams($arr);
            return json_encode($list);
        }

        // working-------------------------------
        public function results($param)
        {
            $exam_id = null;
            $limit = 10;
            $pages = null;
            $currentPage = null;

            if(!empty($param[0])){
                $exam_id = $param[0];
                $currentPage = isset($param[1]) ? $param[1] : 1;
                
                if(!isset($param[2]))
                    $pages = $this->examUserRepository->pageCountByExamId($limit, $exam_id);
                else 
                    $pages = $param[2];
    
                $list = $this->examUserRepository->resultListByExamId($currentPage, $limit, $exam_id);
    
                $view = new view('exam_result_list');
                $view->assign('resultlist', $list);
                $view->assign('exam_id', $exam_id);
                $view->assign('currentPage', $currentPage);
                $view->assign('pages', $pages);
            }
            return;
        }

        
    }

?>