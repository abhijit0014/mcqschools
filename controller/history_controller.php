<?php

    include 'repository/historyRepository.php';
    include 'repository/categoryRepository.php';


    class HistoryController
    {
        private $repository;
        private $categoryRepository;

        function __construct()
        {
            $this->repository = new HistoryRepository();
            $this->categoryRepository = new CategoryRepository();
        }

        public function day()
        {
            $userId = SessionManager::get("user_id");
            $access = false;
            if($userId==4 || $userId==7)
                $access = true;

            $view = new view('history');
            $view->assign('edit_access', $access);
            return;
        }

        public function getData($param) // api
        {
            $month = $param[0];
            $day = $param[1];
            $list = $this->repository->getByDayAndMonth($month, $day);
            return json_encode($list);
        }

        public function add()
        {
            $event = R::dispense( 'history' );
            $view = new view('history_edit');
            $view->assign('event', $event);
            $view->assign('category', R::dispense( 'category' ));
            return;
        }

        public function save()
        {
            $userId = SessionManager::get("user_id");
            if($userId!=4 && $userId!=7){
                header("Location: /history/day");
                exit;
            }

            $this->repository->save($_POST);
            header("Location: /history/day");
            exit;
        }

        public function edit($param)
        {
            $userId = SessionManager::get("user_id");
            if($userId!=4 && $userId!=7){
                header("Location: /history/day");
                exit;
            }

            $event_id = $param[0];

            $event = $this->repository->getById($event_id);
            $category =  $this->categoryRepository->getOne($event->category_id);

            $view = new view('history_edit');
            $view->assign('event', $event);
            $view->assign('category', $category);
            return;
        }

        public function delete($param)
        {
            $event_id = $param[0];
            $this->repository->delete($event_id);
            return;
        }

    }

?>