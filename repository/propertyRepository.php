<?php

    class PropertyRepository
    {
        function __construct()
        {

        }

        public function get()
        {
            return R::findOne( 'property', ' ', [ ] );
        }

        public function save($obj)
        {
            if(!empty($obj['id']))
            {
                $property = R::load( 'property', $obj['id'] );
                $property->live_exam_id = $obj['live_exam_id'];
                $property->updated_by = SessionManager::get("user_id");
                $property->updated_date = date('Y-m-d H:i:s');
                R::store( $property );
            }
        }

    }

?>