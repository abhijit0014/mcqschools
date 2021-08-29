<?php

    class ExamUserRepository
    {
        function __construct()
        {

        }

        public function create($examId, $userId)
        {
            $examUser = R::xdispense('exam_user');
            $examUser->exam_id = $examId;
            $examUser->user_id = $userId;
            $examUser->love = false;
            $examUser->submitted = false;
            $examUser->start_time = date('Y-m-d H:i:s');
            $id =  R::store( $examUser);
            return R::load('exam_user', $id);
        }

        public function getOne($id)
        {
            return R::load( 'exam_user', $id );
        }

        public function getByExamIdAndUserId($examId, $userId)
        {
            return R::findOne('exam_user','exam_id = ? AND user_id = ?', [$examId, $userId]);
        }

        public function delete($id)
        {
            R::trash( 'exam_user', $id );
        }

        public function submit($exam_user_id, $duration)
        {
            $examUser = R::load( 'exam_user', $exam_user_id );
            $examUser->duration = $duration;
            $examUser->submitted = true;
            $examUser->pause = false;
            $examUser->end_time = date('Y-m-d H:i:s');
            return R::store( $examUser);
        }

        public function updateDuration($exam_user_id, $duration, $pause)
        {
            $examUser = R::load( 'exam_user', $exam_user_id );
            $examUser->duration = $duration;
            $examUser->pause = $pause;
            return R::store( $examUser);
        }

        // result list by user_id ------------------------------------------------------
        public function pageCount($limit, $user_id)
        {
            $exam=R::count('exam_user', 'user_id = ?', [$user_id]);
            $totalPages=ceil($exam/$limit);
            return $totalPages;
        }
        
        public function list($page, $limit, $user_id)
        {
            $list = R::getAll("SELECT exam_user.exam_id, exam_user.obtained_marks, exam_user.total_marks, exam_user.start_time, exam_user.submitted, title 
            FROM exam_user left join exam on exam_user.exam_id = exam.id
            WHERE exam_user.user_id = ".$user_id." ORDER BY exam_user.id DESC LIMIT ".(($page-1)*$limit).', '.$limit  );
            return $list;
        }

        // exam result list by exam_id-----------------------------------------------------
        public function pageCountByExamId($limit, $exam_id)
        {
            $exam=R::count('exam_user', 'exam_id = ?', [$exam_id]);
            $totalPages=ceil($exam/$limit);
            return $totalPages;
        }
        
        public function resultListByExamId($page, $limit, $exam_id)
        {
            $list = R::getAll("SELECT users.username, exam_user.start_time,
            exam_user.obtained_marks, exam_user.duration, exam_user.submitted, exam_user.pause
            FROM exam_user left join users on exam_user.user_id = users.id
            WHERE exam_user.exam_id = ".$exam_id." ORDER BY exam_user.obtained_marks DESC LIMIT ".(($page-1)*$limit).', '.$limit  );
            return $list;
        }

        // toppers for test result ---------------------------------
        public function getToppers($exam_id)
        {
            $list = R::getAll("SELECT users.username, exam_user.obtained_marks
            FROM exam_user left join users on exam_user.user_id = users.id
            WHERE exam_user.submitted = true and exam_user.exam_id = ".$exam_id." 
            ORDER BY exam_user.obtained_marks DESC, exam_user.wrong_answered ASC,  exam_user.duration ASC LIMIT 3" );
            return $list;
        }

        // live - top 100 rank ---------------------------------------------
        public function getRank($exam_id, $exam_end_time)
        {
            $list = R::getAll("SELECT users.username, exam_user.obtained_marks
            FROM exam_user left join users on exam_user.user_id = users.id
            WHERE exam_user.obtained_marks > 0 and exam_user.submitted = true 
            and exam_user.end_time <= '".$exam_end_time."' and exam_user.exam_id = ".$exam_id." 
            ORDER BY exam_user.obtained_marks DESC, exam_user.wrong_answered ASC,  exam_user.duration ASC LIMIT 100" );
            return $list;
        }

        // live monthly rank ---------------------------------------------
        public function monthlyTestRank($month, $limit)
        {
            $list = R::getAll("SELECT username, user_id, count(exam_id) as test_count, 
            sum(correct_answered) as correct_ans_count, 
            sum(correct_answered+wrong_answered) as question_count,
            round(sum(obtained_marks)) as obtained_marks
            FROM exam_user  left join users on users.id = exam_user.user_id
            WHERE MONTH(start_time) = MONTH(:month)
            AND YEAR(start_time) = YEAR(CURRENT_DATE())
            group by user_id order by obtained_marks desc limit :limit", array(':month'=>$month, ':limit'=>$limit) );
            return $list;
        }
        
    }

?>