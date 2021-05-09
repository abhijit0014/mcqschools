<?php

    class ExamResultRepository
    {
        function __construct()
        {

        }

        public function save($exam_user_id, $question_id, $ansOption)
        {
            $examResult = R::findOne('exam_result','exam_user_id = ? AND question_id = ?', [$exam_user_id, $question_id]);

            if(empty($examResult)){
                $examResult = R::xdispense( 'exam_result' );
                //$examResult->ans = $obj['ans'];
                $examResult->exam_user_id = $exam_user_id;
                $examResult->question_id = $question_id;
            }

            $examResult->ans_option = $ansOption;
            return R::store( $examResult );
        }

        public function getResult($exam_user_id)
        {
            return R::getAll('SELECT * FROM exam_result WHERE exam_user_id = '.$exam_user_id.' ORDER BY id DESC');
        }

        public function delete($exam_user_id, $question_id)
        {
            $result = R::findOne('exam_result','exam_user_id = ? AND question_id = ?', [$exam_user_id, $question_id]);
            R::trash( 'exam_result', $result->id );
        }


    }

?>