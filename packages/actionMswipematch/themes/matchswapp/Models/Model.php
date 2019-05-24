<?php


namespace packages\actionMswipematch\themes\matchswapp\Models;
use packages\actionMswipematch\Models\Model as BootstrapModel;

\Yii::import('application.modules.aechat.models.*');

class Model extends BootstrapModel {


    public $include_chat = true;

    public function saveMatch(){

        $this->obj_datastorage->set('matches',$this->playid_otheruser);
        $this->obj_datastorage->play_id = $this->playid_otheruser;

        $nickname = $this->getSavedVariable('firstname');

        $this->notifications->addNotification(array(
            'subject' => $nickname .' {#liked you#} ',
            'to_playid' => $this->playid_otheruser,
            'type' => 'liked',
        ));

        if($this->obj_datastorage->valueExists('matches',$this->playid_thisuser)){
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            $this->saveTwoWayMatch();
            return true;
        } else {
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            return false;
        }
    }



}