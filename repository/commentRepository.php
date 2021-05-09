<?php

    class CommentRepository
    {
        function __construct()
        {

        }

        public function save($obj)
        {
            $comment = R::dispense( 'comments' );
            $comment->question_id = $obj['question_id'];
            $comment->comment = $obj['message'];
            $comment->createdBy = SessionManager::get("user_id");
            $comment->createdDate = date('Y-m-d H:i:s');

            $id = R::store( $comment );
            return $id;
        }

        public function getOne($id)
        {
            return R::load( 'comments', $id );
        }

        public function getByQuestionId($question_id,$page,$limit)
        {
            //$list = R::findAll( 'comments' , "WHERE question_id = ".$question_id." ORDER BY id DESC LIMIT ".(($page-1)*$limit).', '.$limit );
            //return json_encode( R::exportAll($list) );
            $list = R::getAll("SELECT comments.id, comments.question_id, comments.comment, comments.created_date, comments.created_by, users.username as fullname
                                FROM comments left join users on comments.created_by = users.id
                                WHERE comments.question_id = ".$question_id." ORDER BY comments.id DESC LIMIT ".(($page-1)*$limit).', '.$limit  );

            return  $list;

        }

        // list for creator --------------------------------------------------
        public function listByExamCreatorId($creator_id, $page, $limit )
        {
            $list = R::getAll("SELECT comments.id, comments.exam_id, comments.question_id, comments.comment, 
            comments.created_by, comments.created_date, question.question, users.username
            FROM comments left join question on comments.question_id = question.id
            left join users on comments.created_by = users.id
            where question.created_by = ".$creator_id." ORDER BY comments.id DESC LIMIT ".(($page-1)*$limit).', '.$limit  );
            return $list;
        }


        public function delete($id)
        {
            return R::trash( "comments", $id );
        }

    }

?>