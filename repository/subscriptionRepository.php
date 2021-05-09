<?php

    class SubscriptionRepository
    {
        function __construct()
        {

        }

        public function subscribe($creator_id)
        {
            $user_id = SessionManager::get("user_id");
            $subscribe = R::findOne( 'subscription', ' creator_id = ? AND user_id = ? ', [ $creator_id, $user_id ] );
            if(empty($subscribe)){
                $subscribe = R::dispense( 'subscription' );
                $subscribe->creator_id = $creator_id;
                $subscribe->user_id = $user_id;
                R::store( $subscribe );

                $user_info = R::findOne( 'users_info', ' user_id = ? ', [ $creator_id ] );
                $user_info->subscribe_count++;
                R::store( $user_info );
            }
        }

        public function unsubscribe($creator_id)
        {
            $user_id = SessionManager::get("user_id");
            $subscribe = R::findOne( 'subscription', ' creator_id = ? AND user_id = ? ', [ $creator_id, $user_id ] );
            if(!empty($subscribe)){
                R::trash('subscription' , $subscribe->id);

                $user_info = R::findOne( 'users_info', ' user_id = ? ', [ $creator_id ] );
                $user_info->subscribe_count--;
                R::store( $user_info );
            }
        }

        public function check_subscription($creator_id){
            $user_id = SessionManager::get("user_id");
            $subscribe = R::findOne( 'subscription', ' creator_id = ? AND user_id = ? ', [ $creator_id, $user_id ] );
            if(!empty($subscribe)) return true;
            return false;
        }

        public function subscription_list()
        {
            $user_id = SessionManager::get("user_id");
            $list = R::getAll( "SELECT users_info.user_id, users_info.display_name, users_info.exam_count, 
            users_info.subscribe_count, users.username FROM users_info  
            join users on users.id = users_info.user_id
            join subscription on users_info.user_id = subscription.creator_id 
            where subscription.user_id = ".$user_id." order by subscription.id desc" );
            return $list;
        }

    }

?>