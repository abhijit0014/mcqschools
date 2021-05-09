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

    }

?>