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

            $event->year = $obj['year'];
            $event->day = $obj['day'];
            $event->month = $obj['month'];
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

        public function getByDayAndMonth($month, $day)
        {
            $list = R::getAll('SELECT history.id, history.title, history.descp, history.year, history.month, 
                            history.day, category.title as category FROM history
                            left join category on category.id = history.category_id
                            where history.month = '.$month.' and history.day = '.$day.'
                            order by year desc'  );

            return  $list;
        }

        public function delete($id)
        {
            return R::trash( "history", $id );
        }

    }

?>