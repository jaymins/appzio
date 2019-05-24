<?php

namespace packages\actionMswipematch\Models;
use Bootstrap\Models\BootstrapModel;


use CException;
use Yii;


Trait Messaging {


    public function initChat(){
        $this->obj_chat = new \Aechat();
        $this->obj_chat->play_id = $this->playid_thisuser;
        $this->obj_chat->gid = $this->appid;
        $this->obj_chat->context = 'mobilematching';
        $this->obj_chat->context_key = $this->actionid;
        $this->obj_chat->game_id = $this->appid;
        $this->obj_chat->initChat();
        $this->chatid = $this->obj_chat->getChatId();
    }

    public function getNotificationCount(){

        if(isset($this->obj_thisuser->notifications)){
            $notifications = unserialize($this->obj_thisuser->notifications);
            foreach($notifications as $notify){
                return 1;
            }
        }

        return false;
    }

    public function getChatContent(){
        $this->initChat();
        return json_encode($this->obj_chat->getChatContent());
    }

    public function resetNotifications(){
        $this->obj_datastorage->deleteAllByAttributes(array('play_id' => $this->playid_thisuser,'value' => $this->playid_otheruser,'key' => 'notifications-match'));
        $this->obj_datastorage->deleteAllByAttributes(array('play_id' => $this->playid_thisuser,'value' => $this->playid_otheruser,'key' => 'notifications-msg'));
    }

    public function getNotifications(){
        $this->initChat();
        $arr = $this->obj_datastorage->get('notifications-match');
        $output = array();

        if(!empty($arr)){
            foreach($arr as $notification){
                $output[] = array(
                    'type' => 'match',
                    'user' => $notification,
                );
            }
        }

        $arr2 = $this->obj_datastorage->get('notifications-msg');

        if(!empty($arr2)){
            foreach($arr2 as $notification){
                $output[] = array(
                    'type' => 'msg',
                    'user' => $notification,
                );
            }
        }

        return $output;
    }

    public static function saveChatContent($recordid){

    }



    public function addNotificationToBanner($type='match'){
        $this->obj_datastorage->play_id = $this->playid_otheruser;
        $this->obj_datastorage->set('notifications-'.$type,$this->playid_thisuser);
    }

    /* saves a notification banner & sends a push */
    public  function saveNotificationForMatch(){

        $this->addNotificationToBanner('match');

        if ( $this->send_accept_push ) {
            $title = 'You have a new match!';

            if($this->firstname_thisuser){
                $name = $this->firstname_thisuser .' matched with you.';
            } else {
                $name = '';
            }

            $msg = $title .' '. $name;

            $this->notifications->addNotification(array(
                'subject' => $msg,
                'to_playid' => $this->obj_otheruser->play_id,
                'type' => 'match',
            ));

            //\Aenotification::addUserNotification( $this->obj_otheruser->play_id, $title, $msg, '+1', $this->appid );
        }

    }


    public function getGroupChats(){

        $sql = "SELECT * FROM ae_chat_users 
                LEFT JOIN ae_chat ON ae_chat_users.chat_id = ae_chat.id
                WHERE `chat_user_play_id` = :playId AND `type` = 'group'";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playId' => $this->playid_thisuser
            ))
            ->queryAll();


        $output = array();

        foreach ($rows as $row){
            $output[] = $row['context_key'];
        }

        return $output;

    }

    public function getMyOutbox( $validity = false ){
        // return $this->obj_datastorage->get('matches');

        $sql = "SELECT * FROM ae_game_play_keyvaluestorage WHERE `play_id` = :play_id AND `key` = 'matches'";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':play_id' => $this->playid_thisuser
            ))
            ->queryAll();

        $output = array();

        foreach ($rows as $row){

            if ( $validity AND !$this->isUserStillValid( $row, $validity ) ) {
                continue;
            }

            if ( $this->checkIfUnmatched( $row['value'], $row['play_id'] ) ) {
                continue;
            }

            if ( $this->checkIfBothMatched( $row['value'], $row['play_id'] ) ) {
                continue;
            }

            $output[] = $row['value'];
        }

        return $output;
    }

    public function getMyInbox( $validity = false, $field = 'matches' ){

        $sql = "SELECT * FROM ae_game_play_keyvaluestorage WHERE `value` = :play_id AND `key` = :field";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':play_id' => $this->playid_thisuser,
                ':field' => $field,
            ))
            ->queryAll();

        $output = array();

        foreach ($rows as $row){
            if ( $validity AND !$this->isUserStillValid( $row, $validity ) ) {
                continue;
            }

            if ( $this->checkIfUnmatched( $row['play_id'], $row['value'] ) ) {
                continue;
            }

            if ( $this->checkIfBothMatched( $row['play_id'], $row['value'] ) ) {
                continue;
            }

            $output[] = $row['play_id'];
        }

        return $output;
    }


    
}