<?php


namespace packages\actionMtasks\Models;
use Appcaching;
use Bootstrap\Models\BootstrapModel;
use packages\actionMearnster\Models\ConfigureMenu;
use packages\actionMnotifications\Models\NotificationsModel;
use packages\actionMproducts\Models\Cart;

class Model extends BootstrapModel {

    public $validation_errors;
    public $taskobj;
    public $cartdata;

    use Cart;
    use Tasks;
    use ConfigureMenu;
    use Proof;

    /* this determines which view we should use using */
    public function getPhase(){
        if($this->sessionGet('task_id')){
            return 4;
        }

        if($this->sessionGet('invitation_id') OR $this->sessionGet('adult_id')){
            return 2;
        }

        return 1;
    }

    public function getChoreListForSelect($type) {

        $result = [];

        switch($type){
            case 'school':
                $type = 'extra_school_work';
                break;

            case 'community':
                $type = 'community_service';
                break;

            case 'other':
                $result[] = ' ; ';
                break;
        }

        $listString = $this->getGlobalVariableByName($type);
        $listParts = explode(',', $listString);

        foreach ($listParts as $listPart) {
            $result[] = $listPart . ';'.$listPart;
        }

        return implode(';', $result);
    }


    public function setPhase($phase){

        if($phase == 1){
            $this->sessionSet('invitation_id', '');
            $this->sessionSet('adult_id', '');
            $this->sessionSet('category', '');
            $this->sessionSet('task_id', '');
        }

    }

    public function validateAdult($email=false,$id=false){

        if(!$email AND !$id){
            $email = strtolower($this->getSubmittedVariableByName('adult_email'));

            if(!$this->validateEmail($email)){
                $this->validation_errors['adult_email'] = '{#please_enter_valid_email#}';
            }

            if(!$this->getSubmittedVariableByName('adult_email')){
                $this->validation_errors['adult_email'] = '{#email_is_required#}';
            }

            if($this->getSubmittedVariableByName('adult_email') == $this->getSavedVariable('email')){
                $this->validation_errors['adult_email'] = '{#this_should_be_adults_email_not_yours#}';
            }
        }

        $name = $this->getSubmittedVariableByName('adult_name');
        $test = explode(' ', $name);

        if(!isset($test[1])){
            $this->validation_errors['adult_name'] = '{#please_enter_full_name#}';
        }

        if(!$this->getSubmittedVariableByName('adult_nickname')){
            $this->validation_errors['adult_nickname'] = '{#nickname_is_required#}';
        }

        if(!$this->getSubmittedVariableByName('adult_name')){
            $this->validation_errors['adult_name'] = '{#name_is_required#}';
        }
    }

    public function getTaskStatus(){
        if($this->sessionGet('category')){
            return 'summary';
        }

        if($this->sessionGet('category')){
            return 'details';
        }

        if($this->sessionGet('adult_id')){
            return 'tasklist';
        }

        return 'default';

    }

    public function saveAdultInvitation($email=false,$id=false){

        if(!$email){
            $email = $this->getSubmittedVariableByName('adult_email');
        }

        $name = $this->getSubmittedVariableByName('adult_name');
        $nickname = $this->getSubmittedVariableByName('adult_nickname');
        $primary = $this->getSubmittedVariableByName('adult_primary', 0);

        if ($primary) {
            $this->removeAllPrimaryContacts();
        } else {

            $invites = TasksInvitationsModel::model()->findAllByAttributes(
                array(
                    'play_id' => $this->playid
                ));

            if (!$invites) {
                $primary = 1;
            }

        }

        $email = strtolower($email);
        $name = ucwords(strtolower($name));
        
        $parent_play = $this->findPlayFromVariable('email', $email);

        if($id){
            $test = TasksInvitationsModel::model()->findByPk($id);
        } else {
            $test = TasksInvitationsModel::model()->findByAttributes(array('play_id' => $this->playid,'email' => $email));
        }

        if(!$test){
            $obj = new TasksInvitationsModel();
            $obj->play_id = $this->playid;
            $obj->email = $email;
            $obj->name = $name;
            $obj->nickname = $nickname;

            if($parent_play){
                $obj->invited_play_id = $parent_play;
            }

            $obj->primary_contact = $primary;
            $obj->status = 'invited';
            $obj->insert();

            $this->cleanAdultCache();
        } else {
            $test->name = $name;
            $test->nickname = $nickname;
            $test->email = $email;
            $test->primary_contact = $primary;
            $test->update();
        }

        if($parent_play){
            $msg = $this->localize('{#earnster_invitation_push#}');
            $msg = str_replace('adult_name', $name, $msg);
            $msg = str_replace('teen_name', $this->getSavedVariable('firstname') . ' ' .$this->getSavedVariable('lastname'), $msg);

            $this->notifications->addNotification(array(
                'subject' => '{#earnster_invitation_to_connect_with#} '. $this->getSavedVariable('firstname'),
                'to_playid' => $parent_play,
                'msg' => $msg,
                'type' => 'invitation',
            ));
        } else {
            $msg = $this->localize('{#earnster_invitation_email#}');
            $msg = str_replace('adult_name', $name, $msg);
            $msg = str_replace('teen_name', $this->getSavedVariable('firstname') . ' ' .$this->getSavedVariable('lastname'), $msg);

            $this->notifications->addNotification(array(
                'subject' => '{#earnster_invitation_to_connect_with#} '. $this->getSavedVariable('firstname'),
                'to_email' => $email,
                'msg' => $msg,
                'type' => 'invitation',
            ));
        }


    }

    protected function removeAllPrimaryContacts() {
        $invites = TasksInvitationsModel::model()->findAllByAttributes(
            array(
                'play_id' => $this->playid
            ));

        if(!$invites){
            return;
        }

        foreach ($invites as $invite) {
            $invite->primary_contact = 0;
            $invite->update();
        }

    }

    public function cleanAdultCache(){
        $this->sessionSet('myadults', '');
    }

    public function getAdultInvitation($id){
        return TasksInvitationsModel::model()->findByPk($id);
    }

    public function getAdults($session=false,$from_active_task=false)
    {

        /**
         * Disabled cache during the development phase
         */
//        $cache = $this->sessionGet('myadults');
//
//        if($cache AND is_array($cache)){
//            return $cache;
//        }

        if (isset($this->taskobj->assignee_id) AND $this->taskobj->assignee_id AND $from_active_task) {
            $adults = TasksInvitationsModel::model()->findAllByAttributes(array('play_id' => $this->playid, 'invited_play_id' => $this->taskobj->assignee_id));
        } elseif (isset($this->taskobj->invitation_id) AND $this->taskobj->invitation_id AND $from_active_task){
            $adults = TasksInvitationsModel::model()->findAllByAttributes(array('id' => $this->taskobj->invitation_id));
        } elseif($this->sessionGet('adult_id') AND $session) {
            $adults = TasksInvitationsModel::model()->findAllByAttributes(array('play_id' => $this->playid, 'invited_play_id' => $this->sessionGet('adult_id')));
        }elseif($this->sessionGet('invitation_id') AND $session){
            $adults = TasksInvitationsModel::model()->findAllByAttributes(array('id' => $this->sessionGet('invitation_id')));
        } else {
            $adults = TasksInvitationsModel::model()->findAllByAttributes(array('play_id' => $this->playid));
        }

        if($adults){
            foreach($adults as $key=>$adult){
                if($adult->invited_play_id){
                    $parent_play = $this->foreignVariablesGet($adult->invited_play_id);
                    if(isset($parent_play['profilepic']) AND $parent_play['profilepic']){
                        $adults[$key]->profilepic = $parent_play['profilepic'];
                    }

                    if(isset($parent_play['username']) AND $parent_play['username']){
                        $adults[$key]->username = $parent_play['username'];
                    }
                    $getTasks = TasksModel::model()->findAllByAttributes(array('owner_id' => $this->playid, 'assignee_id' => $adult->invited_play_id));
                    if (!$getTasks) {
                        $adults[$key]->deals = 0;
                    } else {
                        $adults[$key]->deals = count($getTasks);
                    }
                }
            }

            $this->sessionSet('myadults', $adults);
            return $adults;
        }

        return array();
    }

    public function getRepeatFrequency(){
        $this->setCurrentTask();
        $frequency = $this->taskobj->repeat_frequency;
        $frequency = round(604800 / $frequency,0);
        return $frequency;
    }





}
