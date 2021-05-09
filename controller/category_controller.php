<?php

    include 'repository/categoryRepository.php';


    class CategoryController
    {
        private $repository;

        function __construct()
        {
            $this->repository = new CategoryRepository();
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

    }

?>