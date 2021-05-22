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
            $view = new view('category_form');
            $view->assign('category', $category);
            return;
        }

        public function edit($param)
        {
            if(isset($param[0])){
                $category =  $this->repository->getOne($param[0]);
                $view = new view('category_form');
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

        public function list($param)
        {  
            $categorylist;          
            if(empty($param[0]))
                $categorylist = $this->repository->categoryList();
            else
                $categorylist = $this->repository->searchCategoryByTitle($param[0]);
                
            $view = new view('category_list');
            $view->assign('search_str', empty($param[0]) ? '' : $param[0]);
            $view->assign('categorylist', $categorylist);
            return;
        }

        // quiz by topic or category
        public function search($param)
        {
            $this->repository->updateHitCount($param[0]);
            $view = new view('index');
            $view->assign('category_name', str_replace("-"," ",$param[0]));
            return;
        }

        // category api for autocomplete
        public function autocomplete($param)
        {
            $search_query = $param[0];
            $list = $this->repository->autocompleteList($search_query);
            return  json_encode($list);
        }

        // exam & test list by category
        public function exam($param)
        {
            $category_name = str_replace("-"," ",$param[0]);
            $category =  $this->repository->getByCategoryName($category_name);
            if(empty($category)) header("Location: /");
            $view = new view('category_exam_list');
            $view->assign('category_name', ucfirst($category_name));
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

    }

?>