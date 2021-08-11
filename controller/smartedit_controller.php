<?php

    include 'repository/examRepository.php';
    include 'repository/questionRepository.php';

    class SmarteditController
    {
        private $examRepository;
        private $questionRepository;       

        function __construct()
        {
            $this->examRepository = new ExamRepository();
            $this->questionRepository = new QuestionRepository();
        }

        public function edit()
        {
            $view = new view('smart_edit');
            $view->assign('email', SessionManager::get("email"));
            return;
        }

        public function addExamSlotApi()
        {
            $json = file_get_contents('php://input');
            $data = json_decode($json);
            $exam = $data->exam;
            $questionList = $data->questions;

            $exam = $this->addExam($exam);

            foreach ($questionList as $question) {
                $this->addQuestion($exam, $question);
            }

            return $exam->id;
        }

        private function addExam($exam)
        {
            $examObj = R::dispense( 'exam' );
            $examObj->attemped = 0;
            $examObj->enabled = true;
            $examObj->love = 0;
            $examObj->number_of_question = 0;
            $examObj->published = false;
            $examObj->review = false;
            $examObj->created_by = SessionManager::get("user_id");
            $examObj->created_date = date('Y-m-d H:i:s');

            $examObj->title = $exam->title;
            $examObj->category_id = $exam->category_id;
            $examObj->descp = $exam->descp;
            $examObj->point = $exam->point;
            $examObj->negative_point = $exam->negative_point;
            $examObj->duration_mins = $exam->duration_mins;

            $examObj->id = R::store( $examObj );   
            
            return $examObj;
        }

        private function addQuestion($exam, $question)
        {
            $questionObj = R::dispense( 'question' );
            $questionObj->exam_id = $exam->id;
            $questionObj->enabled = true;
            $questionObj->review = false;
            $questionObj->correct_attempt = 0;
            $questionObj->total_attempt = 0;
            $questionObj->created_date = date('Y-m-d H:i:s');
            $questionObj->created_by = SessionManager::get("user_id");
            $questionObj->question = $question->title;
            $questionObj->option_one = $question->option1;
            $questionObj->option_two = $question->option2;
            $questionObj->option_three = $question->option3;
            $questionObj->option_four = $question->option4;
            $questionObj->ans = $question->ans;
            $questionObj->category_id = $exam->category_id;

            $id = R::store( $questionObj ); 
        }

    }