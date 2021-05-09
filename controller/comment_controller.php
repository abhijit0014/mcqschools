<?php

    include 'repository/commentRepository.php';


    class CommentController
    {
        private $repository;

        function __construct()
        {
            $this->repository = new CommentRepository();
        }

        public function save()
        {
            if(isset($_POST))
                $id = $this->repository->save($_POST);
        }

        public function listByQuestionId($param) // comment list by question_id
        {
            $limit = 10;
            if(!empty($param[0])&!empty($param[1])){
                $list = $this->repository->getByQuestionId($param[0],$param[1],$limit);
                return json_encode($list);
            }
            return;
        }

        public function list($param) // comment list for creator
        {
            $view = new view('comment_list');
            $view->assign('creator_id', $param[0]);
            return;
        }

        // return json comments
        public function listByExamCreatorId($param) // comment list for creator
        {
            $creator_id = null;
            $limit = 10;
            $currentPage = null;
            $creator_id = $param[0];
            $currentPage = isset($param[1]) ? $param[1] : 1;
            $list = $this->repository->listByExamCreatorId($creator_id, $currentPage, $limit);
            return json_encode($list);
        }

        //delete by user
        public function delete($param)
        {
            $comment_id = $param[0];
            $comment = $this->repository->getOne($comment_id);
            if(SessionManager::get("user_id") == $comment->created_by)
                $this->repository->delete($comment_id);
        }

        // delete by creator
        public function deletebycreator($param)
        {
            $comment_id = $param[0];
            $creator_id = $param[1];
            $this->repository->delete($comment_id);
            header("Location: /comment/list/".$creator_id); exit;
        }

    }

?>