<?php

    class ReportRepository
    {
        function __construct()
        {

        }

        public function save($obj)
        {
            $report = R::dispense( 'report' );
            $report->question_id = $obj['question_id'];
            $report->type = $obj['type'];
            $report->report = $obj['report'];
            $report->createdBy = SessionManager::get("user_id");
            $report->createdDate = date('Y-m-d H:i:s');

            $id = R::store( $report );
            return $id;
        }

        public function getOne($id)
        {
            return R::load( 'report', $id );
        }

        public function getByQuestionId($question_id)
        {
            $list = R::findAll( 'report' , "WHERE question_id = ".$question_id." ORDER BY id DESC LIMIT 5 " );
            return json_encode( R::exportAll($list) );
        }

    }

?>