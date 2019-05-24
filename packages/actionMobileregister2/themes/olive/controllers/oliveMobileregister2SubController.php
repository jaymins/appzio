<?php

class oliveMobileregister2SubController extends Mobileregister2Controller {

    /** @var MobileloginModel */
    public $loginmodel;

    public $errors;

    public function olivePhase1(){

        if ( $this->menuid == 'reg_continue' ) {

            $this->saveInterests();

            if ( empty($this->errors) ) {
                $this->saveVariable('reg_phase',2);
                $this->saveVariables();
                $this->olivePhase2();
                return true;
            }

        }

        $this->data->scroll[] = $this->getSpacer( 15 );
        
        $this->collectNickname();
        $this->collectGender();
        $this->collectCity();
        $this->collectInterests();
        $this->collectApproval();

        $this->data->footer[] = $this->getHairline('#cececd');
        $this->data->footer[] = $this->getTextbutton('{#register#}',array('style' => 'olive-submit-button','id' => 'reg_continue','submit_menu_id' => 'saver'));

        return true;
    }

    public function olivePhase2(){
        $this->data = new stdClass();
        //$this->collectLocation();
        $this->setProfilePic();
        $this->setProfileComment();

        if($this->menuid == 'reg_continue_2' AND empty($this->errors)){
            $this->data = new stdClass();
            $this->data->scroll[] = $this->getFullPageLoader();
            $this->saveVariables();
            $this->finishUp();
            $this->data->onload[] = $this->getCompleteAction();
            return true;
        }

        $this->data->footer[] = $this->getHairline('#cececd');
        $this->data->footer[] = $this->getTextbutton('{#register#}',array('style' => 'olive-submit-button','id' => 'reg_continue_2','submit_menu_id' => 'saver'));

    }

    public function collectNickname(){
        if($this->menuid == 'reg_continue') {
            $error = $this->checkForError('screen_name', '{#screen_name_already_taken#} / {#not_valid#}');
        } else {
            $error = false;
        }

        $this->data->scroll[] = $this->formkitField('screen_name','{#nickname#}','{#input_your_nickname#}','nickname',$error);
    }

    public function collectGender(){
        if($this->menuid == 'reg_continue') {
            $error = $this->checkForError('gender', '{#please_choose_your_gender#}');
        } else {
            $error = false;
        }

        $listparams['variable'] = 'gender';
        $interests = array('man'=>'{#male#}','woman'=>'{#female#}');
        $this->data->scroll[] = $this->formkitRadiobuttons('{#gender#}',$interests,$listparams,$error);

    }

    public function collectCity() {
        $cities = array('Aurora' => 'Aurora','Markham' => 'Markham','Newmarket' => 'Newmarket','Richmond Hill' => 'Richmond Hill','Vaughan' => 'Vaughan',
            'Scarborough' => 'Scarborough', 'North York' => 'North York', 'Toronto' => 'Toronto', 'Mississauga' => 'Mississauga');

        $listparams['variable'] = 'city';
        $this->data->scroll[] = $this->formkitRadiobuttons('{#location#}',$cities,$listparams);
    }

    public function collectInterests(){
        $listparams['variable'] = 'temp_interests';
        $interests = array('japanese_food'=>'{#japanese#}','chinese_food'=>'{#chinese#}','deserts' => '點心', 'buffet_food' => '{#buffet#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#food_and_tasting#}',$interests,$listparams,false);
        $interests = array('tennis'=>'{#tennis#}','volleyball'=>'{#volleyball#}','basketball' => '{#basketball#}','sport_chinese' => '羽毛球');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#sport#}',$interests,$listparams,false);
        $interests = array('offroad'=>'{#offroad#}','sports_cars'=>'{#sports_car#}','motorbikes' => '{#motorbike#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#automobile#}',$interests,$listparams,false);
        $interests = array('gardening'=>'{#gardening#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#others#}',$interests,$listparams,false);
    }

    public function collectLocation() {

        if(!$this->getSavedVariable('lat') OR !$this->getSavedVariable('lon')){
            $loc = $this->getCollectLocation();

            if ($loc){
                $this->data->onload[] = $loc;
            }

            
            if($this->menuid == 'reg_continue_2'){
                $this->errors = true;
                $this->data->footer[] = $this->getText('{#please_enable_location_to_finish#}',array('style' => 'register-text-step-error'));
                $this->data->footer[] = $this->getSpacer(8);
            }

        }
        
    }

    public function collectApproval(){
        $error = false;

        if($this->menuid == 'reg_continue') {
            if(!$this->getSubmittedVariableByName('terms_accepted')){
                $this->errors = true;
                $error = '{#please_approve_the_terms#}';
            }
        }

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam('terms_popup');
        $onclick->open_popup = 1;

        $this->data->scroll[] = $this->formkitCheckbox('terms_accepted','{#approve_the_terms#}',array(
            'onclick' => $onclick,
            'type' => 'toggle',
        ), $error);
    }

    public function saveInterests(){
        $savearray = array();
        $var_id = $this->getVariableId( 'temp_interests' ); 
        $handler = $var_id . '_';

        foreach($this->submitvariables as $key => $value){
            if ( stristr($key, $handler) AND !empty($value) ) {
                $id = str_replace( $handler, '', $key );
                $savearray[$id] = $value;
            }
        }

        if ( !empty($savearray) ) {
            $this->deleteVariable( 'temp_interests' );
            $this->saveVariable('interests', json_encode($savearray));
        } else {
            $this->interests_error = '{#please_select_your_interests#}';
            $this->errors = true;
            $this->data->footer[] = $this->getText('{#please_choose_at_least_one_interest#}',array('style' => 'register-text-step-error'));
        }
    }

    public function setProfilePic(){

        if($this->getConfigParam('require_photo')){
            $this->data->scroll[] = $this->getText('{#please_add_profile_info#}', array( 'style' => 'register-text-step-2'));

            if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
                $pic = $this->varcontent['fb_image'];
                $txt = '{#change_the_photo#}';
            } elseif(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
                $pic = $this->varcontent['profilepic'];
                $txt = '{#change_the_photo#}';
            } else {
                $pic = 'filmstrip-placeholder.png';
                $txt = '{#add_a_photo#}';
            }

            //$this->data->scroll[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'),'imgwidth' => '600','imgheight' => '600','imgcrop'=>'yes'));
            $img[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'), 'crop' => 'round', 'width' => '200','text-align' => 'center','floating' => "1",'float' => 'center',
                'border-width' => '5', 'border-color' => '#ffffff','border-radius' => '75'));
            $img[] = $this->getText(' ');

            $this->data->scroll[] = $this->getColumn($img,array('text-align' => 'center','height' => '200','margin' => '8 0 8 0','floating' => "1",'float' => 'center'));

            if($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 'sex-woman')){
                $this->data->scroll[] = $this->getText('{#uploading#} ...', array( 'style' => 'uploading_text'));
            }

            $this->data->scroll[] = $this->getTextbutton($txt, array(
                'variable' => $this->vars['profilepic'],
                'action' => 'upload-image',
                'sync_upload'=>true,
                'max_dimensions' => '900',
                'style' => 'olive-reset-button' ,
                'id' => $this->vars['profilepic']));

            if($this->menuid == 'reg_continue_2' AND $pic == 'filmstrip-placeholder.png'){
                $this->errors = true;
                $this->data->footer[] = $this->getText('{#please_upload_image#}',array('style' => 'register-text-step-error'));
                $this->data->footer[] = $this->getSpacer(8);
            }

        }
    }

    public function setProfileComment() {
        if ( !$this->getConfigParam('require_comment') ) {
            return false;
        }

        if($this->getSubmittedVariableByName('profile_comment')){
            $commentcontent = $this->getSubmittedVariableByName('profile_comment');
        } elseif(isset($this->varcontent['profile_comment'])) {
            $commentcontent = $this->varcontent['profile_comment'];
        } elseif($this->getSavedVariable('instagram_bio')) {
            $commentcontent = $this->getSavedVariable('instagram_bio');
        } else {
            $commentcontent = '';
        }

        if($this->getSavedVariable('role') == 'brand') {
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#company_description#} ({#required#})', 'style' => 'olive-textarea'));
        } else {
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#profile_comment#} ({#required#})', 'style' => 'olive-textarea'));
        }

        if($this->menuid == 'reg_continue_2' AND !$commentcontent){
            $this->errors = true;
            $this->data->footer[] = $this->getText('{#please_input_profile_comment#}',array('style' => 'register-text-step-error'));
            $this->data->footer[] = $this->getSpacer(8);
        }

    }

    public function finishUp(){

        $this->saveVariable( 'new_registration', 1 );

        if(!$this->getSavedVariable('gender')){
            $arr['gender'] = 'man';
        }

        $arr['logged_in'] = 1;
        $arr['look_for_men'] = 1;
        $arr['look_for_women'] = 1;
        $arr['men'] = 1;
        $arr['women'] = 1;
        $arr['country'] = 'Canada';
        $arr['notify'] = '1';
        $arr['name'] = $this->getSavedVariable('real_name');
        $arr['reg_phase'] = 'complete';

        AeplayVariable::saveNamedVariablesArray($arr,$this->playid,$this->gid,'update');
        $this->loadVariableContent();

        $this->initMobileMatching();
        $this->mobilematchingobj->turnUserToItem(true,__FILE__);

        Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
        MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);

    }

}