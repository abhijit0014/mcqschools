<?php

    class WebStatusRepository
    {
        function __construct()
        {

        }

        public function get_status()
        {
            $new_update_flag = false;
            $status = R::findOne( 'webstatus' );
            if(empty($status)){
                $new_update_flag = true;
            }else{
                $current_time = date('Y-m-d');
                $last_updated_time = date("Y-m-d",  strtotime($status->created_date));
                if($current_time > $last_updated_time)
                    $new_update_flag = true;
            }

            if($new_update_flag){
                $status = R::dispense( 'webstatus' );
                $status->user = R::count( 'users' );;
                $status->atmp_test = R::count( 'exam_user' );
                $quiz_attempted = R::getCell( 'SELECT sum(total_attempt) as attempted FROM question' );
                $status->atmp_question = R::count( 'exam_result' ) + (int) $quiz_attempted;
                $status->created_date = date('Y-m-d H:i:s'); 
                R::store($status);
            }
            
            return $status;
        }

    }

?>