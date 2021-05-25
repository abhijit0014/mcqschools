<?php

    class CategoryRepository
    {
        function __construct()
        {

        }

        public function save($obj)
        {
            $category;
            if(empty($obj['id'])){
                $category = $this->getByCategoryName($obj['title']);
                if(empty($category)){
                    $category = R::dispense( 'category' );
                    $category->createdDate = date('Y-m-d H:i:s');
                    $category->createdBy = SessionManager::get("user_id");
                }
            }else{
                $category = $this->getOne($obj['id']);
                $category->updatedDate = date('Y-m-d H:i:s');
                $category->updatedBy = SessionManager::get("user_id");
            }

            $category->title = ucfirst($obj['title']);
            $category->enabled = $obj['enabled'] == 'true' ? true : false;
    
            if(SessionManager::get("user_role")=='admin')
                return R::store( $category );
            return;
        }

        public function getOne($id)
        {
            return R::load( 'category', $id );
        }

        public function updateHitCount($categoryTitle)
        {
            $category = R::findOne( 'category', ' title = ? ', [ $categoryTitle ] );
            if(!empty($category)){
                $category->hit_count++;
                R::store( $category );
            }
        }

        public function getByCategoryName($categoryTitle)
        {
            return R::findOne( 'category', ' title = ? ', [ $categoryTitle ] );
        }

        public function categoryList()
        {
            return R::getAll( " SELECT * FROM category ORDER BY id DESC LIMIT 50" );
        }

        public function searchCategoryByTitle($str_search)
        {
            return R::getAll( "SELECT * FROM category WHERE TITLE LIKE '%".$str_search."%' ORDER BY hit_count DESC LIMIT 50 " );
        }

        public function autocompleteList($str_search)
        {
            return R::getAll( "SELECT * FROM category WHERE TITLE LIKE '%".$str_search."%' ORDER BY hit_count DESC LIMIT 10 " );
        }

    }

?>