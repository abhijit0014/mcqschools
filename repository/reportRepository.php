<?php

    class ReportRepository
    {
        function __construct()
        {

        }

        public function save($obj)
        {
            $user_id = SessionManager::get("user_id");
            $report = R::dispense( 'report' );

            // update exam report
            if(!empty($obj['exam_id'])){
                $exam_id = $obj['exam_id'];

                $report_count = R::count('report', 'exam_id = ?', [$exam_id]);
                if($report_count>4) return;

                $tempReport = $this->getByExamIdAndUserId($exam_id, $user_id);
                if(!empty($tempReport)){
                    $tempReport->type = $obj['type'];
                    $tempReport->report = empty($obj['report'])? " ": $obj['report'];
                    $tempReport->solved = false;
                    $tempReport->createdDate = date('Y-m-d H:i:s');
                    return R::store( $tempReport );
                }else{
                    $report->exam_id = $obj['exam_id'];
                }
            }

            // update question report
            if(!empty($obj['question_id'])){
                $question_id = $obj['question_id'];

                $report_count = R::count('report', 'question_id = ?', [$question_id]);
                if($report_count>$GLOBALS['UNSOLVED_REPORT_LIMIT']) return;

                $tempReport = $this->getByQuestionIdAndUserId($question_id, $user_id);
                if(!empty($tempReport)){
                    $tempReport->type = $obj['type'];
                    $tempReport->report = empty($obj['report'])? " ": $obj['report'];
                    $tempReport->createdDate = date('Y-m-d H:i:s');
                    $tempReport->solved = false;
                    return R::store( $tempReport );
                }else{
                    $report->question_id = $obj['question_id'];
                }
            }

            // new report
            $report->type = $obj['type'];
            $report->report = empty($obj['report'])? " ": $obj['report'];
            $report->createdBy = SessionManager::get("user_id");
            $report->createdDate = date('Y-m-d H:i:s');
            $report->solved = false;

            return R::store( $report );
        }

        public function getOne($id)
        {
            return R::load( 'report', $id );
        }

        public function getByExamIdAndUserId($exam_id, $user_id)
        {
            return R::findOne( 'report', ' exam_id = ? AND created_by = ? ', [ $exam_id, $user_id ] );
        }
        public function getByQuestionIdAndUserId($question_id, $user_id)
        {
            return R::findOne( 'report', ' question_id = ? AND created_by = ? ', [ $question_id, $user_id ] );
        }

        public function getByQuestionId($question_id)
        {
            $list = R::findAll( 'report' , "WHERE question_id = ".$question_id." ORDER BY id DESC LIMIT 5 " );
            return json_encode( R::exportAll($list) );
        }

        // exam report list :: list for creator --------------------------------------------------
        public function reportByCreatorId($creator_id, $page, $limit )
        {
            $list = R::getAll("SELECT report.id, report.exam_id, report.question_id, report.report, 
            report.type, report.created_by as creator_id, report.created_date, 
            question.question, exam.title as exam, 
            users.username as reporter_name
            FROM report left join question on report.question_id = question.id
            left join exam on report.exam_id = exam.id
            left join users on report.created_by = users.id
            where (question.created_by = ".$creator_id." or exam.created_by = ".$creator_id.") And solved = false
            ORDER BY report.id DESC LIMIT ".(($page-1)*$limit).', '.$limit  );
            
            return $list;
        }

        //solved -----------------------------------------------------------------------------------
        public function solved($id)
        {
            $report =  R::load( 'report', $id );
            if(!empty($report)){
                $report->solved = true;
                return R::store( $report );
            }
            return;
        }






        //admin ----------------------------------------------------------------------------------

        // question
        public function questionReportList()
        {
            return R::getAll('SELECT DISTINCT question.id, question.question FROM question
                            left join report on report.question_id = question.id
                            where question.enabled = true and report.solved = false 
                            order by report.created_date DESC limit 100');
        }

        public function questionReportsById($question_id)
        {
            return R::getAll('SELECT * FROM report where question_id = '.$question_id.' order by created_date DESC limit 100');
        }

        public function blockQuestion($question_id){
            R::exec('UPDATE question SET enabled = false WHERE id = ? ',array($question_id));
            return;
        }


        // exam
        public function examReportList()
        {
            return R::getAll('SELECT DISTINCT exam.id, exam.title FROM exam
                            left join report on report.exam_id = exam.id
                            where exam.enabled = true and report.solved = false 
                            order by report.created_date DESC limit 100');
        }

        public function examReportsById($exam_id)
        {
            return R::getAll('SELECT * FROM report where exam_id = '.$exam_id.' order by created_date DESC limit 100');
        }

        public function examReportsByIdAndUnsolved($exam_id)
        {
            return R::getAll('SELECT * FROM report where exam_id = '.$exam_id.' and solved = false order by created_date DESC limit 100');
        }

        public function blockExam($exam_id){
            R::exec('UPDATE exam SET enabled = false WHERE id = ? ',array($exam_id));
            return;
        }



        // review request -------------------------------------------------------------------
        public function examReviewRequest($exam_id){
            R::exec('UPDATE exam SET review = true WHERE id = ? ',array($exam_id));
            return;
        }

        public function questionReviewRequest($question_id){
            R::exec('UPDATE question SET review = true WHERE id = ? ',array($question_id));
            return;
        }


    }

?>