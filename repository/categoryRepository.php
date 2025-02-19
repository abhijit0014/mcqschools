<?php

    class CategoryRepository
    {
        function __construct()
        {

        }

        public function save($obj)
        {
            $category = null;
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
            $category->parent_id = $obj['parent_id'];
    
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

        // all category list
        public function allCategoryList()
        {
            return R::getAll( " SELECT * FROM category where exam_avl=true or question_avl=true ORDER BY title" );
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
            return R::getAll( "SELECT * FROM category WHERE TITLE LIKE '%".$str_search."%' ORDER BY hit_count DESC LIMIT ".$GLOBALS['CATEGORY_AUTOCOMPLETE_RESULT_LIMIT'] );
        }


        // for search result page
        public function availableCategoryList($str_search)
        {
            return R::getAll( "SELECT * FROM category WHERE question_avl = true and TITLE LIKE '%".$str_search."%' ORDER BY hit_count DESC LIMIT ".$GLOBALS['CATEGORY_AUTOCOMPLETE_RESULT_LIMIT'] );
        }

        public function getSubCategoryListByParentIdForQuiz($parent_id)
        {
            return R::getAll( " SELECT * FROM category where question_avl = true and parent_id=".$parent_id );
        }
        public function getSubCategoryListByParentIdForExam($parent_id)
        {
            return R::getAll( " SELECT * FROM category where exam_avl = true and parent_id=".$parent_id );
        }







        // admin ----------------------------
        public function getSubCategoryList($parent_id)
        {
            return R::getAll( " SELECT * FROM category where parent_id=".$parent_id );
        }
        public function getParentCategoryList()
        {
            return R::getAll( " SELECT * FROM category where parent_id is null or parent_id = 0 order by title" );
        }
    }

?>