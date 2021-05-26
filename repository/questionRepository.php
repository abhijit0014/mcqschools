<?php

    class QuestionRepository
    {
        function __construct()
        {

        }

        public function save($obj)
        {
            $question;

            if(!empty($obj['id']))
            {
                $question = R::load('question', $obj['id']);
                $question->updated_by = SessionManager::get("user_id");
                $question->updated_date = date('Y-m-d H:i:s');
            }else{
                $question = R::dispense( 'question' );
                $question->exam_id = $obj['exam_id'];
                $question->enabled = true;
                $question->review = false;
                $question->correct_attempt = 0;
                $question->total_attempt = 0;
                $question->created_date = date('Y-m-d H:i:s');
                $question->created_by = SessionManager::get("user_id");
            }
            
            $question->question = $obj['question'];
            $question->option_one = $obj['optionOne'];
            $question->option_two = $obj['optionTwo'];
            $question->option_three = $obj['optionThree'];
            $question->option_four = $obj['optionFour'];
            $question->ans = $obj['ans'];
            $question->category_id = $obj['categoryId'];


            $id = R::store( $question );
            return $id;
        }

        public function getOne($id)
        {
            return R::load( 'question', $id );
        }

        public function delete($id)
        {
            R::exec('DELETE FROM exam_result WHERE question_id = ? ',array($id));
            R::trash( 'question', $id );
        }
        
        public function questionCountByExamId($examId){
            return R::count( 'question', 'WHERE exam_id = ?', [ $examId ] );
        }

        public function updateQuestionAttemptCounter($question_id, $attempt){
            $sql;
            if($attempt =='true'){
                R::exec('UPDATE question SET correct_attempt = correct_attempt + 1, total_attempt = total_attempt + 1 WHERE id = ? ',array($question_id));
            }else{
                R::exec('UPDATE question SET total_attempt = total_attempt+1 WHERE id = ? ',array($question_id));
            }
        }

        public function pageCount($examId, $limit)
        {
            $questions=R::count( 'question', 'WHERE exam_id = ?', [ $examId ] );
            $totalPages=ceil($questions/$limit);
            return $totalPages;
        }

        public function list($examId, $page, $limit)
        {
            return R::getAll('SELECT * FROM question WHERE exam_id = '.$examId.' ORDER BY id DESC LIMIT '.(($page-1)*$limit).', '.$limit);
        }

        public function getByExamId($examId)
        {
            return R::getAll('SELECT * FROM question WHERE exam_id = '.$examId.' ORDER BY id DESC');
        }




        // for quiz - based on category

        public function questionCountByCategoryId($limit, $category_id)
        {
            $questions = R::count( 'question', 'WHERE category_id = ?', [ $category_id ] );
            $totalPages=ceil($questions/$limit);
            return $totalPages;
        }

        public function questionListByCategoryId($page, $limit, $category_id)
        {
            return R::getAll('SELECT id, question, option_four, option_one, option_three, option_two, ans, total_attempt, correct_attempt FROM question WHERE category_id = '.$category_id.' ORDER BY id DESC LIMIT '.(($page-1)*$limit).', '.$limit);
        }

    }

?>