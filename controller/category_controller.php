<?php

    include 'repository/categoryRepository.php';
    include 'repository/examRepository.php';


    class CategoryController
    {
        private $repository;

        function __construct()
        {
            $this->repository = new CategoryRepository();
            $this->examRepository = new ExamRepository();
        }

        public function add()
        {
            $category = R::dispense( 'category' );
            $view = new view('admin/category_form');
            $view->assign('category', $category);
            return;
        }

        public function edit($param)
        {
            if(isset($param[0])){
                $category =  $this->repository->getOne($param[0]);
                $view = new view('admin/category_form');
                $view->assign('category', $category);
            }
            return;
        }

        public function save_process()
        {
            if(isset($_POST)){
                $this->repository->save($_POST);
                header("Location: /category/list/".$_POST['title']);
                exit;
            }
        }




        // category api for autocomplete - API
        public function autocomplete($param)
        {
            $search_query = $param[0];
            $list = $this->repository->autocompleteList($search_query);
            return  json_encode($list);
        } 
        

        // -----------------------------------------------------------------------

        // all category list
        public function browse()
        {  
            $categorylist = $this->repository->allCategoryList();
            $view = new view('category_browse');
            $view->assign('categorylist', $categorylist);
            return;
        }

        // test list
        public function testList()
        {  
            $categorylist = $this->repository->allCategoryList();
            $view = new view('menu_test');
            return;
        }

        // quiz list
        public function quizList()
        {  
            $categorylist = $this->repository->allCategoryList();
            $view = new view('menu_quiz');
            return;
        }

        // syllabus
        public function syllabus()
        {  
            $categorylist = $this->repository->allCategoryList();
            $view = new view('menu_syllabus');
            return;
        }




        // quiz by topic or category
        public function quiz($param)
        {
            $this->repository->updateHitCount($param[0]);
            $view = new view('quiz');
            $view->assign('category_name', str_replace("-"," ",$param[0]));
            return;
        }

        // exam list based on category
        public function exam($param)
        {
            $category_name = str_replace("-"," ",$param[0]);
            $category =  $this->repository->getByCategoryName($category_name);

            if(empty($category)){ 
                header("Location: /"); exit; 
            }

            $view = new view('category_exam_list');
            $view->assign('category_name', $category->title);
            $view->assign('category_id', $category->id);
            return;
        }

        public function examByCategoryApi($param)
        {
            $limit = 10;
            $category_id = isset($param[0]) ? $param[0] : 1;
            $currentPage = isset($param[1]) ? $param[1] : 1;
            $list = $this->examRepository->listByCategoryId($currentPage, $limit, $category_id);
            return json_encode($list);
        }


        //-------------------------------------------------------------------------------------------------
        // category list page for ADMIN
        public function list($param)
        {  
            $categorylist;          
            if(empty($param[0]))
                $categorylist = $this->repository->categoryList();
            else
                $categorylist = $this->repository->searchCategoryByTitle($param[0]);
                
            $view = new view('admin/category_list');
            $view->assign('search_str', empty($param[0]) ? '' : $param[0]);
            $view->assign('categorylist', $categorylist);
            return;
        }

        // update category listing information
        public function categoryInfoUpdate()
        {  
            $categorylist = R::getAll( " SELECT * FROM category" );  

            foreach ($categorylist as $category) 
            {
                $exam = R::findOne( 'exam', ' enabled = true and published = true and category_id = ? ', [ $category['id'] ] );
                if($exam) R::exec('UPDATE category SET exam_avl = true WHERE id = ?', [ $category['id'] ]);
                else R::exec('UPDATE category SET exam_avl = false WHERE id = ?', [ $category['id'] ]);

                $question = R::findOne( 'question', ' enabled = true and category_id = ? ', [ $category['id'] ] );
                if($question && !in_array($category['title'],  $GLOBALS['BLOCKED_CATEGORIES']))  
                    R::exec('UPDATE category SET question_avl = true WHERE id = ? ', [ $category['id'] ]);
                else R::exec('UPDATE category SET question_avl = false WHERE id = ? ', [ $category['id'] ]);
            }

            return "update completed";
        }

    }

?>