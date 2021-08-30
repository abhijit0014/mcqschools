<?php

    class ExamRepository
    {
        function __construct()
        {

        }

        public function save($obj)
        {
            $exam;

            // save exam
            if(empty($obj['id'])){
                $exam = R::dispense( 'exam' );
                $exam->id = $obj['id'];
                $exam->attemped = 0;
                $exam->enabled = true;
                $exam->love = 0;
                $exam->number_of_question = 0;
                $exam->published = false;
                $exam->review = false;
                $exam->created_by = SessionManager::get("user_id");
                $exam->created_date = date('Y-m-d H:i:s');
            }else{

                $exam = R::load( 'exam', $obj['id'] );
                if(SessionManager::get("user_id") != $exam->created_by) 
                    return;
                $exam->updated_by = SessionManager::get("user_id");
                $exam->updated_date = date('Y-m-d H:i:s');  
            }

            $exam->title = $obj['title'];
            $exam->category_id = empty($obj['category_id']) ? 145 : $obj['category_id'];
            $exam->descp = $obj['descp'];
            $exam->point = $obj['point'];
            $exam->negative_point = $obj['negative_point'];
            $exam->duration_mins = $obj['duration_mins'];
            if($obj['start_time'])
            $exam->start_time = $obj['start_time'];
            if($obj['end_time'])
            $exam->end_time = $obj['end_time'];

            $id = R::store( $exam );
            return $id;
        }

        public function getOne($id)
        {
            return R::load( 'exam', $id );
        }

        public function countExamByCreatorId($creator_id)
        {
            return R::count( 'exam', 'created_by = ? and published = ?', [$creator_id, true] );
        }

        public function checkAllowedExamLimit()
        {
            $creator_id = SessionManager::get("user_id");
            $count =  R::count( 'exam', "created_by = ? And created_date BETWEEN CONCAT(CURDATE(), ' ', '00:00:00') AND CONCAT(CURDATE(), ' ', '23:59:59')", [$creator_id] );

            if($count<$GLOBALS['EXAM_ADITION_LIMIT_PER_DAY']){
                return true;
            }
            return false;
        }

        // for search bar ------------------------------------------------------------
        public function searchByTitle($str_search, $page, $limit)
        {
            $list = R::getAll( "Select * from exam WHERE published = true AND enabled = true AND TITLE LIKE '%".$str_search."%' ORDER BY id DESC LIMIT ".(($page-1)*$limit).', '.$limit);
            return $list;
        }


        // exam list by created_by ------------------------------------------------------
        public function pageCount($limit, $created_by)
        {
            $exam=R::count('exam', 'created_by = ?', [$created_by]);
            $totalPages=ceil($exam/$limit);
            return $totalPages;
        }

        public function list($page, $limit, $created_by)
        {
            $list=R::getAll('select * from exam WHERE created_by = '.$created_by.' ORDER BY id DESC LIMIT '.(($page-1)*$limit).', '.$limit);
            return $list;
        }

        public function publishedExamList($page, $limit, $created_by)
        {
            $list=R::getAll('select * from exam WHERE created_by = '.$created_by.' AND published = true ORDER BY id DESC LIMIT '.(($page-1)*$limit).', '.$limit);
            return $list;
        }


        // list By Subscription --------------------------------------------------
        public function listBySubscription($user_id, $page, $limit )
        {
            $list = R::getAll("SELECT exam.id as exam_id, exam.title, exam.attemped, exam.created_date, 
            exam.duration_mins, exam.number_of_question, exam_user.id as exam_user_id
            FROM exam 
            left join subscription on exam.created_by = subscription.creator_id
            left join exam_user on exam.id = exam_user.exam_id
            where exam.published = true and exam.enabled = true and subscription.user_id = ".$user_id." 
            order by exam.created_date desc LIMIT ".(($page-1)*$limit).', '.$limit  );
            return $list;
        }

        // list of suggested exams at home page --------------------------------------------------
        public function listOfSuggestedExams()
        {
            $list = null;

            $user_id = SessionManager::get("user_id");
            if($user_id){
                $list = R::getAll("SELECT exam.id, exam.title, exam.attemped, exam.number_of_question, exam.duration_mins, 
                exam.created_date, category.title as category FROM exam
                left join category on category.id = exam.category_id
                Left JOIN exam_user on exam.id = exam_user.exam_id and exam_user.user_id = ".$user_id."
                where category_id in ( select  distinct category_id from exam
                left join exam_user on exam.id = exam_user.exam_id
                where exam_user.user_id = ".$user_id." order by exam_user.id desc) and 
                exam_user.submitted Is NULL and exam.enabled = true 
                and exam.published = true order by exam.created_date desc limit 10");             
            }

            if(!$list){
                $list = R::getAll("SELECT exam.id, exam.title, exam.attemped,number_of_question, exam.duration_mins, 
                exam.created_date, category.title as category FROM exam 
                left join category on category.id = exam.category_id
                where exam.enabled = true and exam.published = true order by exam.created_date desc limit 10");
            }

            return $list;
        }

        // list by category id -------------------------------------------------------
        public function examListByCategoryId($page, $limit, $categoryId)
        {
            $user_id = SessionManager::get("user_id");
            if(!$user_id) $user_id = 0;

            $list=R::getAll('select exam.id, title, number_of_question, duration_mins, attemped, exam.created_date,
            exam_user.user_id, exam_user.submitted from exam 
            Left JOIN exam_user on exam_user.exam_id = exam.id and exam_user.user_id = '.$user_id.'
            WHERE  exam.category_id in (select id from category where parent_id = '.$categoryId.') 
            AND exam.published = true and exam.enabled = true 
            ORDER BY exam.created_date DESC LIMIT '.(($page-1)*$limit).', '.$limit);

            if(!$list)
            $list=R::getAll('select exam.id, title, number_of_question, duration_mins, attemped, exam.created_date,
            exam_user.user_id, exam_user.submitted from exam 
            Left JOIN exam_user on exam_user.exam_id = exam.id and exam_user.user_id = '.$user_id.'
            WHERE  exam.category_id = '.$categoryId.'  
            AND exam.published = true and exam.enabled = true 
            ORDER BY exam.created_date DESC LIMIT '.(($page-1)*$limit).', '.$limit);

            return $list;
        }

        // get live quiz --------------------------------------------------------------
        public function getLiveQuiz(){
            $start_time = date("Y-m-d");
            $end_time = $start_time." 23:59:59";
            //return R::findOne( 'exam', 'date(start_time) >= CURRENT_DATE() AND date(start_time) < DATE_ADD(CURDATE(), INTERVAL +1 DAY) AND created_by = ? ', [ 417 ] );
            return R::findOne( 'exam', "created_by = ? and start_time BETWEEN '".$start_time."' AND '".$end_time."' ", [ 417 ] );
        }


        // delete exam ------------------------------------------------------------
        public function delete($exam_id)
        {
            R::exec('DELETE FROM exam_result WHERE exam_user_id IN (SELECT id FROM exam_user WHERE exam_id = ?)', array($exam_id));
            R::exec('DELETE FROM exam_user WHERE exam_id = ? ',array($exam_id));
            R::trash( 'exam', $exam_id );
        }

    }

?>