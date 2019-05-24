<?php

/*
	This is a theme specific subcontroller.


 notice the class naming here, needs to adhere to this standard and extend the main controller*/

Yii::import('application.modules.aelogic.packages.actionMobilefeedbacktool.models.*');

class MobileteamadminController extends ArticleController {

    /* @var MobileteamadminModel */
    public $obj_team;

    /* @var MobileteamadminmembersModel */
    public $obj_team_members;

    public $data_team;
    public $data_team_member;

    public $team_id;
    public $team_role;

    public $data;
    public $invites;
    public $team_member_count;
    public $team_members;

    public function tab1(){
        $this->data = new stdClass();
        $this->initObjects();

        if($this->scnearioJoiningTeam()){
            return $this->data;
        }

        if($this->getConfigParam('complete_action')){
            $this->scenarioComplete();
        } else {
            $this->scenarioComplete();
        }

        return $this->data;
    }

    /* aka help */
    public function tab2(){
        $this->data = new stdClass();
        $this->tabbing();
        $this->setHeader();
        $this->data->scroll[] = $this->getText('{#back#}',array('style' => 'gifit-button','onclick' => $this->getOnclick('tab1',false)));
        return $this->data;
    }


    /* aka edit name */
    public function tab3(){
        $this->data = new stdClass();
        $this->initObjects();
        $this->tabbing();

        if($this->menuid == 'name-saver'){
            $this->data_team->title = $this->getSubmittedVariableByName('new-name');
            $this->data_team->update();
            $this->no_output = true;
            return $this->data;
        }

        $onclick[] = $this->getOnclick('id',false,'name-saver');
        $onclick[] = $this->getOnclick('tab1',false);

        $this->teamHeaderEdit();
        $col[] = $this->getText('{#cancel#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('tab1',false,'send-invite')));
        $col[] = $this->getText('{#save#}',array('style' => 'gifit-button-footer','onclick' => $onclick));

        $this->data->footer[] = $this->getRow($col,array('text-align'=> 'center'));
        return $this->data;

    }

    /* aka fundamentals */
    public function tab4(){
        $this->data = new stdClass();
        $this->tabbing();

        $this->initObjects();
        $this->smallHeader('{#fundamentals#}','{#subtext_for_fundamentals#}');

        $this->areas();
        return $this->data;
    }

    /* aka joining other teams */
    public function tab5(){
        $this->data = new stdClass();

        if($this->scnearioJoiningTeam()){
            return $this->data;
        }
        $this->tabbing();
        $this->initObjects();
        $this->teamInvitations();
        return $this->data;

    }

    public function initObjects(){

        $this->obj_team = new MobileteamadminModel();
        $this->obj_team->factoryInit($this);

        $this->obj_team_members = new MobileteamadminmembersModel();
        $this->obj_team_members->factoryInit($this);

        $this->data_team = MobileteamadminModel::model()->findByPk($this->getSavedVariable('gifit_team_id'));

        if(isset($this->data_team->id)){
            $this->team_id = $this->data_team->id;
            $this->data_team_member = MobileteamadminmembersModel::model()->findByAttributes(array('team_id' => $this->data_team->id, 'play_id'=>$this->playid));
            if($this->data_team_member->role_type){
                $this->team_role = $this->data_team_member->role_type;
            }

            $this->team_members = MobileteamadminmembersModel::model()->findAllByAttributes(array('team_id' => $this->data_team->id));

        }

        $this->invites = $this->obj_team->findInvitations();

    }


    /* when its part of the registration */
    public function scenarioComplete(){

        switch($this->menuid){
            case 'save-name':
                $this->addTeam();
                break;

            case 'skip-invite':
                $this->addTeam();
                break;

            case 'send-invite':
                $this->sendInvites();
                break;

            default:
                if($this->getSavedVariable('gifit_team_id')){
                    $this->teamEditMain();
                } else {
                    $this->setHeader();
                    $this->setNaming();
                }
                break;
        }
    }

    public function scnearioJoiningTeam(){

        if(strstr($this->menuid,'join-team-')){
            $id = str_replace('join-team-','',$this->menuid);
            $obj = MobileteamadminmembersModel::model()->findByAttributes(array('play_id'=>$this->playid,'team_id'=>$id));

            if(is_object($obj) AND ($obj->status == 'active' OR $obj->status == 'member')){
                $this->saveVariable('gifit_team_id',$id);
                $this->loadVariableContent(true);
                $this->initObjects();
                $this->smallHeader('{#team_switched#}');
                $col[] = $this->getText('{#main#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('tab1')));
                $this->data->footer[] = $this->getColumn($col,array('text-align' => 'center'));
                return true;
            }
        }

        if($this->menuid=='save-name'){
            $this->addTeam();
            return true;
        }elseif($this->menuid == 'validate-code'){
            $this->smallHeader('{#joining_team#}');
            $id = $this->sessionGet('joining-team');
            $email = strtolower($this->getSavedVariable('email'));
            $invitecode = trim(strtolower($this->getSubmittedVariableByName('invitation_code')));

            $obj = MobileteamadminmembersModel::model()->findByAttributes(array('team_id' => $id,'email' => $email,'invite_code' => $invitecode));
            if(!is_object($obj)){
                $this->data->scroll[] = $this->getText('{#sorry#}',array('style' => 'gifit-titletext-header-white'));
                $this->data->scroll[] = $this->getText('{#wrong_code_or_invitation_not_found#} :(',array('style' => 'gifit-titletext-header-white'));
                $col[] = $this->getText('{#back#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'cancel')));
                $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center'));
                return true;
            } else {
                /* adding user to the team */
                $obj->status = 'member';
                $obj->play_id = $this->playid;
                $obj->update();

                $this->saveVariable('gifit_team_id',$id);

                if($this->getConfigParam('complete_action')){
                    $this->data->onload[] = $this->getOnclick('complete-action');
                    return true;
                } else {
                    return false;
                }
            }
        }

        if(strstr($this->menuid,'join-team-')){
            $id = (int)str_replace('join-team-','',$this->menuid);
            if($id > 0){
                $this->enterJoinCode($id);
                return true;
            }
        }

        if(!$this->team_id AND $this->menuid != 'create-team-instead'){
            if($this->invites){
                $this->teamInvitations();
                $this->createOwnTeam();
                return true;
            }
        }

        return false;
    }

    public function enterJoinCode($id){
        $obj = MobileteamadminModel::model()->findByPk($id);
        if(!is_object($obj)) return false;
        $this->sessionSet('joining-team',$id);
        $this->smallHeader('{#join#}');

        $this->data->scroll[] = $this->getText('{#enter_the_code_from_your_invitation_to_join#}',array('style' => 'gifit-titletext-header-white'));
        //$this->data->scroll[] = $this->getText('{#note',array('style' => 'gifit-titletext-header-white'));
        $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('invitation_code'),array('style' => 'gifit-name-field','variable' => 'invitation_code'));
        $col[] = $this->getText('{#cancel#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'cancel')));
        $col[] = $this->getText('{#join#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'validate-code')));
        $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center'));
    }



    public function teamInvitations(){
        $this->smallHeader('{#invitation_to_join#}');
        $term = $this->getConfigParam('complete_action') ? '{#join_team#}' : '{#switch_to_team#}';

        foreach($this->invites as $invitation){
            $obj = MobileteamadminModel::model()->findByPk($invitation->team_id);
            if(is_object($obj) AND $obj->owner_id != $this->playid AND $obj->id != $this->getSavedVariable('gifit_team_id')){
                $vars = AeplayVariable::getArrayOfPlayvariables($obj->owner_id);
                if(isset($vars['real_name'])){
                    $this->data->scroll[] = $this->getText($vars['real_name'] .' {#has_invited_you_to_join_team#} ' .$obj->title,array('style' => 'gifit-titletext-header-white'));
                    $col[] = $this->getText($term .' "' .$obj->title .'"',array('style' => 'gifit-button-join','onclick' => $this->getOnclick('id',false,'join-team-'.$obj->id)));
                    $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center','padding' => '15 50 0 50'));
                    unset($col);
                }
            }
        }

        if(!$this->getConfigParam('complete_action')){
            $obj = MobileteamadminModel::model()->findByAttributes(array('owner_id' => $this->playid));
            if(is_object($obj) AND $this->getSavedVariable('gifit_team_id') != $obj->id){
                $col[] = $this->getText($term.' "'.$obj->title .'"',array('style' => 'gifit-button-join','onclick' => $this->getOnclick('id',false,'join-team-'.$obj->id)));
                $col[] = $this->getRow($col,array('text-align'=> 'center'));
                $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center','padding' => '15 50 0 50'));
            }
        }
    }

    public function createOwnTeam(){
        $this->data->scroll[] = $this->getSpacer(10);
        $this->data->scroll[] = $this->getText('{#or#}',array('style' => 'gifit-titletext-header-white'));
        $this->data->scroll[] = $this->getText('{#create_your_own_team_instead#}',array('style' => 'gifit-titletext-header-white'));
        $col[] = $this->getText('{#create_team#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'create-team-instead')));
        $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center'));
    }

    public function tabbing(){

        if($this->getConfigParam('complete_action')){
            return false;
        }

        $tabs = array('tab1' => '{#members#}');

        if($this->team_role == 'admin'){
            $tabs['tab4'] = '{#fundamentals#}';
        }

        if(count($this->invites) > 1){
            $tabs['tab5'] = '{#change_team#}';
        }

        if(count($tabs) > 1){
            $this->data->scroll[] = $this->getTabs($tabs);
        }
        


    }

    public function teamEditMain($edit=false){

        $this->tabbing();

        if(!is_object($this->data_team)){
            $this->setHeader();
            $this->setNaming();
            return $this->data;
        }

        $this->teamHeader();
        $this->inviteGrid();
    }


    public function sendInvites(){

        $error = array();

        foreach ($this->submitvariables as $key=>$var){

            if(strstr($key,'email') AND $var){
                $number = str_replace('email','',$key);

                $email = $this->submitvariables['email'.$number];
                $name = mb_substr($email,0,strpos($email,'@'));

                if(stristr($name,'.')){
                    $names = explode('.',$name);
                    $firstname = $names[0];
                    $lastname = $names[1];
                } else {
                    $firstname = $name;
                    $lastname = '';
                }

                if($this->validateEmail($email)) {
                    MobilefeedbacktoolModel::invalidateCache($this->gid,$this->playid);
                    $this->sendInvite($email,$firstname,$lastname);
                    $this->initObjects();
                } else {
                    $error[$number] = '{#please_check_email#}';
                }
            }
        }

        if(!empty($error)){
            $this->tabbing();
            $this->teamHeader();
            $this->inviteGrid($error);
        } else {
            $this->tabbing();
            $this->teamHeader();
            $this->data->scroll[] = $this->getText('{#invites_sent#}',array('style' => 'gifit-general-text'));
            if($this->getConfigParam('complete_action')){
                $this->data->scroll[] = $this->getFullPageLoader();
                $this->data->onload[] = $this->getOnclick('complete-action');
            } else {
                $this->inviteGrid();
            }
        }
    }

    public function inviteGrid($error=false){
        if($this->team_role == 'admin' AND $this->getConfigParam('complete_action')){
            $this->data->scroll[] = $this->getText('{#invite_team_members#}', array('style' => 'gifit-titletext-white'));
            $this->data->scroll[] = $this->getText('{#users_will_receive_their_invitations_via_email#}', array('style' => 'gifit-general-text'));
            $this->inviteMembers($error);
        }elseif($this->team_role == 'admin') {
            $this->data->scroll[] = $this->getText('{#invite_team_members#}', array('style' => 'gifit-titletext-white'));
            $this->data->scroll[] = $this->getText('{#users_will_receive_their_invitations_via_email#}', array('style' => 'gifit-general-text'));
            $this->inviteMembers($error);
            $this->teamMembersListing();
        } else {
            $this->data->scroll[] = $this->getText('{#team_members#}', array('style' => 'gifit-titletext-white'));
            $this->teamMembersListing();
        }

    }

    public function inviteMembers($error=false){

        if($this->team_role != 'admin'){
            return true;
        }

        $count = count($this->team_members);

        if($count >= $this->data_team->license){
            $this->data->scroll[] = $this->getText('{#buy_license_to_add_more_people#}',array('style' => 'gifit-general-text'));
        } else {
            if(isset($this->data_team_member->role_type) AND $this->data_team_member->role_type == 'admin'){
                while($count < $this->data_team->license){

                    if(isset($error[$this->team_member_count])){
                        $style = 'gifit-invite-field-error';
                    } else {
                        $style = 'gifit-invite-field';
                    }

                    $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('email'.$count),array(
                        'style' => $style,'variable' => 'email'.$count,'hint' => '{#email#}','input_type' => 'email'));

                    if(isset($error[$count])) {
                        $this->data->scroll[] = $this->getText($error[$count], array('style' => 'gifit-general-text-error'));
                    }
                    unset($col);
                    $count++;
                    if(!$this->getConfigParam('complete_action')){
                        break;
                    }
                }
            }
        }

        if($this->getConfigParam('complete_action')){
            $col[] = $this->getText('{#skip#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('complete-action')));
            $col[] = $this->getText('{#send_invites#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'send-invite')));
            $this->data->footer[] = $this->getRow($col,array('text-align'=> 'center'));
        } else {
            $col[] = $this->getText('{#send_invites#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'send-invite')));
            $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center'));
        }

    }

    public function teamMembersListing(){
        $this->team_member_count = 1;

        if(strstr($this->menuid,'change-status-')){
            $id = str_replace('change-status-','',$this->menuid);
            $this->obj_team_members->changeUserRole($id);
            $this->initObjects();
        }

        if(strstr($this->menuid,'do-user-delete-')){
            $id = str_replace('do-user-delete-','',$this->menuid);
            MobileteamadminmembersModel::model()->deleteByPk($id);
            $this->initObjects();
        }

        if(strstr($this->menuid,'confirm-delete-user-')){
            $deluser = str_replace('confirm-delete-user-','',$this->menuid);
        }


        foreach ($this->team_members as $member) {

            $change_permission = $this->getOnclick('id',false,'change-status-'.$member->id);
            $delete = $this->getOnclick('id',false,'confirm-delete-user-'.$member->id);

            $col[] = $this->getText($member->email, array(
                'style' => 'gifit-grid-field', 'variable' => 'email', 'hint' => '{#email#}', 'input_type' => 'email'));

            if($this->data_team_member->role_type == 'admin'){
                $col[] = $this->getText($member->role_type, array(
                    'style' => 'gifit-grid-field', 'variable' => 'email', 'hint' => '{#email#}', 'input_type' => 'email',
                    'onclick' => $change_permission));
            } else {
                $col[] = $this->getText($member->role_type, array(
                    'style' => 'gifit-grid-field', 'variable' => 'email', 'hint' => '{#email#}', 'input_type' => 'email'
                    ));
            }

            $col[] = $this->getText('{#' . $member->status . '#}', array(
                'style' => 'gifit-grid-field', 'variable' => 'email', 'hint' => '{#email#}', 'input_type' => 'email'));

            if($this->data_team_member->role_type == 'admin') {
                $col[] = $this->getImage('trash-delete-icon.png', array('height' => '20', 'vertical-align' => 'middle', 'onclick' => $delete));
            }

            if(empty($error)) {
                if(isset($deluser) AND $member->id == $deluser){
                    $this->data->scroll[] = $this->getRow($col, array('text-align' => 'center', 'margin' => '10 0 0 0',
                        'opacity' => '0.8','vertical-align' => 'middle','background-color' => '#FF7272'));
                } else {
                    $this->data->scroll[] = $this->getRow($col, array('text-align' => 'center', 'margin' => '10 0 0 0', 'opacity' => '0.6','vertical-align' => 'middle'));
                }
            }
            $this->team_member_count++;
            unset($col);
        }

        if(strstr($this->menuid,'confirm-delete-user-')){
            $this->deleteUserConfirm($deluser);
        }

    }


    public function deleteUserConfirm($id){
        $this->data->footer[] = $this->getText('{#are_you_sure_you_want_to_delete_this_user#}?',array('style' => 'gifit-title-explanation'));
        $col[] = $this->getText('{#cancel#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('tab1',false,'cancel')));
        $col[] = $this->getText('{#delete#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'do-user-delete-'.$id)));
        $this->data->footer[] = $this->getRow($col,array('text-align'=> 'center'));

    }



    public function smallHeader($title,$subtext=false){
        $this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
        $this->data->scroll[] = $this->getText($title,array('style' => 'gifit-titletext-header'));

        if($subtext){
            $this->data->scroll[] = $this->getText($subtext,array('style' => 'gifit-titletext-header-subtext'));
        }
        $this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
    }

    public function teamHeader(){
        $this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));

        if(isset($this->data_team_member->role_type) AND $this->data_team_member->role_type == 'admin'){
            $this->data->scroll[] = $this->getText($this->data_team->title,array('style' => 'gifit-titletext-header','onclick' => $this->getOnclick('tab3',true)));
        } else {
            $this->data->scroll[] = $this->getText($this->data_team->title,array('style' => 'gifit-titletext-header'));
        }

    }

    public function teamHeaderEdit(){
        $this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
        $name = isset($this->data_team->title) ? $this->data_team->title : '{#new_team#}';
        $this->data->scroll[] = $this->getFieldtext($name,array('style' => 'gifit-titletext-header','activation' => 'initially','variable' => 'new-name'));
    }




    public function sendInvite($email,$firstname,$lastname){
        $appname = isset($this->appinfo->name) ? $this->appinfo->name : 'Appzio';
        $subject = '{#join#} '.$appname;
        $code = ucfirst(Helper::generateShortcode());

        $this->obj_team_members->addInvite($email,$firstname,$lastname,$this->team_id,$code);
        $message = $this->getSavedVariable('real_name') .' {#invites_you_to_try_gifit#}, {#a_smart_feedback_tool#}.';

        //$body = $this->localizationComponent->smartLocalize('{#register_for#} '.$appname);
        //$body .= "<br /><br />";

        $body = $this->localizationComponent->smartLocalize($message .'. {#install#} ' .$appname .' {#to_access_your_feedbacks#}.');
        $body .= "<br /><br />";

        if(isset($this->mobilesettings->appstore_url) AND isset($this->mobilesettings->playstore_url) AND $this->mobilesettings->appstore_url AND
            $this->mobilesettings->playstore_url){
            $body .= $this->localizationComponent->smartLocalize('{#download_for_ios#}');
            $body .= "<br />";
            $body .= $this->mobilesettings->appstore_url;
            $body .= "<br />";
            $body .= "<br />";

            $body .= $this->localizationComponent->smartLocalize('{#download_for_android#}');
            $body .= "<br />";
            $body .= $this->mobilesettings->playstore_url;
            $body .= "<br />";
            $body .= "<br />";

            $body .= '<b>' .$this->localizationComponent->smartLocalize('{#code_to_join_my_team#}') .'</b>';
            $body .= "<br />";
            $body .= $code;
            $body .= "<br />";
            $body .= "<br />";

        }

        $body .= $this->localizationComponent->smartLocalize('{#best#},');
        $body .= "<br />";
        $body .= $this->localizationComponent->smartLocalize('{#email_signature_message#}');

        Aenotification::addUserEmail( $this->playid, $subject, $body, $this->gid, $email );
    }



    public function addTeam(){

        if($this->getSubmittedVariableByName('team_name')){
            $create = $this->obj_team->createNewTeam($this->getSubmittedVariableByName('team_name'));

            if($create){
                $this->saveVariable('gifit_team_id',$create);
                $this->initObjects();
                $this->teamEditMain();
                return true;
            } else {
                $error = '{#team_name_already_reserved#}';
            }
        } else {
            $error = '{#please_enter_name_to_continue#}';
        }

        $this->setHeader();
        $this->setNaming($error);
    }

    public function setNaming($error=false){
        $this->data->scroll[] = $this->getText('{#name_your_team#}',array('style' => 'gifit-titletext-white'));

        if($error){
            $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('team_name'),array('style' => 'gifit-name-field-error','variable' => 'team_name'));
            $this->data->scroll[] = $this->getText($error,array('style' => 'gifit-error'));
        } else {
            $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('team_name'),array('style' => 'gifit-name-field','variable' => 'team_name'));
        }

        $this->data->scroll[] = $this->getText('{#continue#}',array('style' => 'gifit-button','onclick' => $this->getOnclick('id',false,'save-name')));

        /* cancelling is possible only if the previous action was team joining */
        if($this->menuid == 'create-team-instead'){
            $this->data->scroll[] = $this->getText('{#cancel#}',array('style' => 'gifit-button','onclick' => $this->getOnclick('id',false,'cancel')));
        }
    }


    public function setHeader(){

        $this->copyAssetWithoutProcessing('gifit-swipe1.png');
        $this->copyAssetWithoutProcessing('gifit-swipe2.png');
        $this->copyAssetWithoutProcessing('gifit-swipe3.png');

        $width = $this->screen_width;
        $height = $this->screen_width;

        $items[] = $this->getColumn($this->intro1(),array('background-image' => 'gifit-swipe1.png',
            'width' => $width,'height' => $height,'background-size' => 'cover','vertical-align' => 'top'));
        $items[] = $this->getColumn($this->intro2(),array('background-image' => 'gifit-swipe2.png',
            'width' => $width,'height' => $height,'background-size' => 'cover','vertical-align' => 'top'));
        $items[] = $this->getColumn($this->intro3(),array('background-image' => 'gifit-swipe3.png',
            'width' => $width,'height' => $height,'background-size' => 'cover','vertical-align' => 'top'));

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getSwipearea($items),
            ), array( 'margin' => '0 0 0 0' )),
        ), array( 'margin' => '0 0 0 0' ));

    }

    public function intro1(){
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText('{#create_your_team#}',array('style' => 'gifit-titletext'));
        $col[] = $this->getSpacer(20);
        $col[] = $this->getText('{#team_explanation_one#}',array('style' => 'gifit-title-explanation'));
        return $col;
    }

    public function intro2(){
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText('{#how_it_works#}?',array('style' => 'gifit-titletext'));
        $col[] = $this->getSpacer(20);
        $col[] = $this->getText('{#team_explanation_two#}',array('style' => 'gifit-title-explanation'));
        return $col;
    }

    public function intro3(){
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText('{#enteprise_product#}',array('style' => 'gifit-titletext'));
        $col[] = $this->getSpacer(20);
        $col[] = $this->getText('{#team_explanation_three#}',array('style' => 'gifit-title-explanation'));
        return $col;
    }


    public function areas(){

        if(stristr($this->menuid,'confirmdelete-')){
            $id = str_replace('confirmdelete-','',$this->menuid);
            $this->deleteConfirm($id);
            return true;
        }

        if(stristr($this->menuid,'dodelete-')){
            $id = str_replace('dodelete-','',$this->menuid);
            $this->doDelete($id);
        }

        if($this->menuid == 'save-area' AND $this->getSubmittedVariableByName('newfundamental')){
            $obj = new MobilefeedbacktoolfundamentalsModel();
            $obj->game_id = $this->gid;
            $obj->team_id = $this->team_id;
            $obj->title = $this->getSubmittedVariableByName('newfundamental');
            $obj->insert();
        }

        $this->listAreas();
        $this->addArea();
    }

    public function listAreas($hilite=false){
        $areas = MobilefeedbacktoolfundamentalsModel::model()->findAllByAttributes(array('game_id' => $this->gid,'team_id' => $this->team_id),array('order' => 'title'));

        if(!empty($areas)){
            $this->data->scroll[] = $this->getText('{#current_areas#}',array('style' => 'gifit-title-explanation'));
            $this->data->scroll[] = $this->getSpacer(15);

            $style_field = array('text-align' => 'center','font-size' => '14',);
            $style_row = array('margin' => '5 50 5 50','background-color' => '#ffffff',
                'border-radius' => '8','padding' => '12 10 12 10','opacity' => '0.7');

            foreach ($areas as $area){

                if($area->id == $hilite) {
                    $style_row['border-color'] = '#FC0100';
                    $style_row['border-width'] = '2';
                    $style_row['background-color'] = '#FCAAA3';
                } else {
                    unset($style_row['border-color']);
                    unset($style_row['border-width']);
                    $style_row['background-color'] = '#ffffff';
                }

                $delete = $this->getOnclick('id',false,'confirmdelete-'.$area->id);

                $col[] = $this->getText($area->title,array(
                    'style' => $style_field,'variable' => 'newfundamental','hint' => '{#name_your_area#}'));
                $col[] = $this->getImage('trash-delete-icon.png',array('width' => '20','floating' => '1','float' => 'right'
                ,'onclick' => $delete));
                $this->data->scroll[] = $this->getRow($col,array('style' => $style_row));
                unset($col);

            }
        }
    }

    public function deleteConfirm($id){
        $this->listAreas($id);
        $this->data->footer[] = $this->getText('{#are_you_sure_you_want_to_delete_this_area#}?',array('style' => 'gifit-title-explanation'));
        $col[] = $this->getText('{#cancel#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('tab1',false,'cancel')));
        $col[] = $this->getText('{#delete#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'dodelete-'.$id)));
        $this->data->footer[] = $this->getRow($col,array('text-align'=> 'center'));

    }

    public function doDelete($id){
        if($id){
            MobilefeedbacktoolfundamentalsModel::model()->deleteAllByAttributes(array('game_id' => $this->gid,'team_id' => $this->team_id,'id' => $id));
        }
    }


    public function addArea(){
        $this->data->scroll[] = $this->getSpacer(20);
        $this->data->scroll[] = $this->getText('{#add_a_new_area#}',array('style' => 'gifit-title-explanation'));

        $style = array('margin' => '15 50 5 50','background-color' => '#ffffff','text-align' => 'center','font-size' => '14',
            'border-radius' => '8');

        $this->data->scroll[] = $this->getFieldtext('',array(
            'style' => $style,'variable' => 'newfundamental','hint' => '{#name_your_area#}'));

        $col[] = $this->getText('{#save#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'save-area')));
        $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center'));
    }


}