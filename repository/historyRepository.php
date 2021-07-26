<?php

    class HistoryRepository
    {
        function __construct()
        {
        }

        public function save($obj)
        {
            if(empty($obj['id'])){
                $event = R::dispense( 'history' );
                $event->enabled = true;
                $event->createdBy = SessionManager::get("user_id");
                $event->createdDate = date('Y-m-d H:i:s');
            }else{
                $event = R::load( 'history', $obj['id'] );
                $event->updated_by = SessionManager::get("user_id");
                $event->updated_date = date('Y-m-d H:i:s');
            }

            $event->event_date = $obj['event_date'];
            $event->title = $obj['title'];
            $event->descp = $obj['descp'];
            $event->category_id = $obj['category_id'];

            $id = R::store( $event );
            return $id;
        }

        public function getById($id)
        {
            return R::load( 'history', $id );
        }

        public function getAll()
        {
            return R::getAll('SELECT history.id, history.title, history.descp, history.event_date,
            category.title as category FROM history
            left join category on category.id = history.category_id
            order by event_date desc');
        }

        public function getByDayAndMonth($month, $day)
        {
            $list = R::getAll('SELECT history.id, history.title, history.descp, history.event_date,
                            category.title as category FROM history
                            left join category on category.id = history.category_id
                            where Month(history.event_date) = '.$month.' and Day(history.event_date) = '.$day.'
                            order by event_date desc'  );
            return  $list;
        }

        public function getImportantDay()
        {
            date_default_timezone_set('Asia/Kolkata');

            return R::getAll('SELECT history.id, history.title, history.descp, history.event_date,
            category.title as category FROM history
            left join category on category.id = history.category_id
            where Month(history.event_date) = '.date('m').' and Day(history.event_date) = '.date('d').' 
            and category.title = "important day" '  );
        }

        public function delete($id)
        {
            return R::trash( "history", $id );
        }

    }

?>