<?php

    include 'repository/categoryRepository.php';
    include 'repository/examRepository.php';


    class SearchController
    {
        private $repository;

        function __construct()
        {
            $this->categoryRepository = new CategoryRepository();
            $this->examRepository = new ExamRepository();
        }

        // autocomplete search api
        public function autocomplete($param)
        {
            $search_str = $param[0];
            $list = R::GetAll( "SELECT * from search_query WHERE query_str LIKE '%".$search_str."%' ORDER BY search_count DESC LIMIT 5 " );
            return json_encode( $list );
        }

        // result page
        public function query($param)
        {
            $search_str = $param[0];
            $view = new view('search_result');
            $view->assign('query', str_replace('-', ' ', $search_str));
        }

        // result api
        public function query_result_api($param)
        {
            // get result
            $limit = 10;
            $search_query = $param[0];
            $page_number = isset($param[1]) ? $param[1] : 1;
            $categorylist = [];
            
            if($page_number==1)
            $categorylist = $this->categoryRepository->availableCategoryList($search_query);
            $examList = $this->examRepository->searchByTitle($search_query, $page_number, $limit);

            // update query
            if($page_number==1)
            if(!empty($categorylist) || !empty($examList)){
                $search_str = strtolower($param[0]);
                $query = R::findOne( 'search_query', ' query_str = ? ', [ $search_str ] );
                if(empty($query)){
                    $query = R::xdispense("search_query");
                    $query->query_str = $search_str;
                    $query->search_count = 0;
                    $query->created_date = date('Y-m-d H:i:s');
                }else{
                    $query->search_count++;
                }
                R::store($query);
            }
            
            $result = []; 
            $result['categorylist'] = $categorylist;
            $result['examList'] = $examList;
            return json_encode( $result );
        }

    }

?>