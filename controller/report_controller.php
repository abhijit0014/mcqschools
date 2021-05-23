<?php

    include 'repository/reportRepository.php';


    class ReportController
    {
        private $repository;

        function __construct()
        {
            $this->repository = new ReportRepository();
        }

        public function save()
        {
            if(isset($_POST))
                $id = $this->repository->save($_POST);
        }

        public function list($param) // report list for creator
        {
            $view = new view('report_list');
            return;
        }

        // return json reports
        public function listByCreatorId($param) 
        {
            $creator_id = SessionManager::get("user_id");;
            $limit = 10;
            $currentPage = null;
            $currentPage = isset($param[0]) ? $param[0] : 1;
            $list = $this->repository->reportByCreatorId($creator_id, $currentPage, $limit);
            return json_encode($list);
        }

    }

?>