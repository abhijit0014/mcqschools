<?php

    include 'repository/examRepository.php';
    include 'repository/examUserRepository.php';
    include 'repository/categoryRepository.php';

    class QuizController
    {
        private $categoryRepository; 
        private $examRepository;
        private $examUserRepository;       

        function __construct()
        {
            $this->categoryRepository = new CategoryRepository();
            $this->examRepository = new ExamRepository();
            $this->examUserRepository = new ExamUserRepository();
        }

        public function live($param)
        {
            $exam = null;
            $toppers = [];
            $current_time = 0;
            $start_time = 0;
            $quiz_id = 0;
            $exam_stop_flag = false;

            $exam =  $this->examRepository->getLiveQuiz();
            if($exam)
            {
                $quiz_id = $exam->id;
                
                date_default_timezone_set('Asia/Kolkata');
                $current_time = date('Y-m-d H:i:s');
                $start_time = date("Y-m-d H:i:s",  strtotime($exam->start_time));
                $end_time = date("Y-m-d H:i:s",  strtotime($exam->end_time));

                // auto publish before 5min
                if(!$exam->published){
                    $min_diff = round((strtotime($current_time) - strtotime($start_time)) / 60,0);
                    if($min_diff > -10){
                        $exam->published = true;
                        $exam->created_date = date('Y-m-d H:i:s');
                        R::store( $exam );
                    }
                }

                // exam stop
                if($current_time >  $end_time){
                    $exam_stop_flag = true;
                    $toppers = $this->examUserRepository->getRank($exam->id, $exam->end_time);
                }
                
            }

            $view = new view('live_quiz');
            $view->assign('exam',  $exam);
            $view->assign('toppers', $toppers);
            $view->assign('current_time',  $current_time);
            $view->assign('start_time',   $start_time);
            $view->assign('exam_stop_flag',   $exam_stop_flag);
            $view->assign('quiz_id',   $quiz_id);
            return;
        }

        public function category($param)
        {
            $categoryName = str_replace("-"," ",$param[0]);
            $subCategoryList = null;

            $category = $this->categoryRepository->getByCategoryName($categoryName);
            if($category){
                $this->categoryRepository->updateHitCount($param[0]);
                $subCategoryList = $this->categoryRepository->getSubCategoryListByParentIdForQuiz($category->id); 
                if(!$subCategoryList && $category->parent_id)
                $subCategoryList = $this->categoryRepository->getSubCategoryListByParentIdForQuiz($category->parent_id); 
            }

            $view = new view('quiz_new');
            $view->assign('category_name', $categoryName);
            $view->assign('subCategoryList', json_encode($subCategoryList));
            return;
        }

    }