<?php

    include 'repository/examRepository.php';
    include 'repository/questionRepository.php';
    include 'repository/categoryRepository.php';
    include 'model/questionResponseModel.php';


    class QuestionController
    {
        private $repository;
        private $examRepository;
        private $categoryRepository;

        function __construct()
        {
            $this->repository = new QuestionRepository();
            $this->examRepository = new ExamRepository();
            $this->categoryRepository = new CategoryRepository();
        }

        public function add($param)
        {
            $question = R::dispense( 'question' );
            $question->exam_id = $param[0];
            $view = new view('question_edit');
            $view->assign('question',  $question);
            $view->assign('category', R::dispense( 'category' ));
            return;
        }

        // question edit
        public function edit($param)
        {
            if(isset($param[0]))
            $question =  $this->repository->getOne($param[0]);
            $category =  $this->categoryRepository->getOne($question->category_id);

            $view = new view('question_edit');
            $view->assign('question', $question);
            $view->assign('category', $category);
            return;
        }

        public function save()
        {
            if(isset($_POST)){
                $exam =  $this->examRepository->getOne($_POST['exam_id']);
                if(SessionManager::get("user_id")==$exam->created_by)

                if($this->repository->checkAllowedQuestionLimit()){
                    $id = $this->repository->save($_POST);
                    header("Location: /question/list/".$_POST['exam_id']); 
                    exit;
                }else{
                    header("Location: /question/list/".$_POST['exam_id']."?error=true&msg=You can add maximum". $GLOBALS['QUESTION_ADITION_LIMIT_PER_DAY']." question per day"); 
                    exit;
                }
            }
        }

        // duplicate question check api
        public function isQuestionExist($param)
        {
            $data = json_decode( file_get_contents('php://input') );
            if(isset($data)){
                $question =  $this->repository->isQuestionExist($data);
                return json_encode($question);
            }

            return null;
        }

        // question correction 
        public function correction($param)
        {
            if(isset($param[0]))
            $question =  $this->repository->getOne($param[0]);
            $category =  $this->categoryRepository->getOne($question->category_id);

            $view = new view('question_correction');
            $view->assign('question', $question);
            $view->assign('category', $category);
            return;
        }

        public function correction_save()
        {
            if(isset($_POST)){
                $exam =  $this->examRepository->getOne($_POST['exam_id']);
                if(SessionManager::get("user_id")==$exam->created_by){
                    $id = $this->repository->save($_POST);
                    header("Location: /report/list/");
                    exit;
                }
            }
        }


        // update question attampt counter
        public function api_uq_status($param)
        {
            if(SessionManager::csrf_verify() && isset($param[0]) && isset($param[1])){
                $question_id = $param[0];
                $attempt_status = $param[1];
                $question = $this->repository->updateQuestionAttemptCounter($question_id, $attempt_status);
            }
        }

        public function delete($param)
        {
            $examId = $param[0];
            $questionId = $param[1];
            $question =  $this->repository->getOne($questionId);

            if($question->created_by == SessionManager::get("user_id"))
                $this->repository->delete($questionId);

            header("Location: /question/list/".$examId);
            exit;
        }

        public function list($param)
        {
            $examId = $param[0];;
            $limit = 10;
            $pages = null;
            $currentPage = isset($param[1]) ? $param[1] : 1;

            $exam =  $this->examRepository->getOne($examId);
            if($exam->created_by != SessionManager::get("user_id")){
                header("Location: /exam/list"); 
                exit;
            }
            
            if(!isset($param[2])) 
                $pages = $this->repository->pageCount($examId, $limit);
            else  
                $pages = $param[2];

            $questionlist = $this->repository->list($examId, $currentPage, $limit);
            
            $view = new view('question_list');
            $view->assign('exam', $exam);
            $view->assign('questionlist', $questionlist);
            $view->assign('currentPage', $currentPage);
            $view->assign('pages', $pages);
            return;
        }

        
        
        
        
        
        // for quiz - based on category
        // rest call
        // return json list
        public function question_list_api($param)
        {
            $json_str = file_get_contents('php://input');
            $post_data = json_decode($json_str);

            if(empty($post_data)) return;

            $limit = 50;
            $currentPage = 0;
            $category_id = 0;
            $question_list = [];

            // set current_page
            $currentPage = !empty($post_data->next_page) ? $post_data->next_page : 1;
            
            // set category
            $category = !empty($post_data->category) ? $post_data->category : 'Quiz';

            // set category_id
            $category_id = $post_data->category_id;
            if(empty($category_id)){
                $cat =  $this->categoryRepository->getByCategoryName($category);
                if($cat) $category_id = $cat->id;
            }

            if(!empty($category_id ))
                $question_list = $this->repository->questionListByCategoryId($currentPage,$limit,$category_id);


            //return response objet
            return json_encode(new QuestionResponseModel($currentPage, $category_id, $question_list));
        }


    }

?>