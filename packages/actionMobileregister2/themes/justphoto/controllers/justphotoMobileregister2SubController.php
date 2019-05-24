<?php


Yii::import('application.modules.aelogic.packages.actionMobilefeedbacktool.models.*');


class justphotoMobileregister2SubController extends Mobileregister2Controller {

    /** @var MobileloginModel */
    public $loginmodel;
    public $errors;

    public $userlist;

    /* @var MobilefeedbacktoolModel */
    public $dataobj;

    public function initUserlist(){
        $this->dataobj = new MobilefeedbacktoolModel();
        $this->dataobj->gid = $this->gid;
        $this->dataobj->author_id = $this->playid;
        $this->dataobj->playid = $this->playid;
        $this->userlist = $this->dataobj->initUserlist($this->getConfigParam('userdata'));
        $this->initLoginModel();
    }


    public function tab2(){
        return true;
    }

    public function setHeader(){
        if($this->getConfigParam('actionimage1')){
            $image = $this->getConfigParam('actionimage1');
            $this->data->header[] = $this->getImage($image);
        }

        $this->data->scroll[] = $this->getSpacer('10');
    }


    public function setName(){
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getText('{#first_name#}',array('text-align' => 'center','margin' => '5 0 5 0'));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('firstname'),array('font-ios' => 'Roboto','text-align' => 'center','variable' => 'firstname'));

        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getText('{#last_name#}',array('text-align' => 'center','margin' => '5 0 5 0'));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('lastname'),array('font-ios' => 'Roboto','text-align' => 'center','variable' => 'lastname'));
    }
    
    public function setDepartments(){
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getText('{#choose_your_department#}',array('text-align' => 'center','margin' => '5 0 5 0'));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getFieldlist($this->userlist['departments'],array('font-ios' => 'Roboto','variable' => 'department','value' => $this->getSubmittedVariableByName('department')));
    }

    public function validate(){

        $firstname = strtolower($this->getSubmittedVariableByName('firstname'));
        $lastname = strtolower($this->getSubmittedVariableByName('lastname'));
        $department = $this->getSubmittedVariableByName('department');
        $fullname = ucfirst($firstname) .' ' .ucfirst($lastname);

        if(isset($this->userlist['names'][$fullname]['department']) AND $this->userlist['names'][$fullname]['department'] == $department){
            return true;
        } else {
            false;
        }

    }

    public function collectLocation()
    {

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


    public function justphotoPhase1(){
        $this->data = new stdClass();
        $this->saveVariable('reg_phase',1);

        /* check whether the user already exists */
        $this->initUserlist();
        $user = $this->dataobj->findUser($this->varcontent);

        if($user){
            $this->loginmodel->switchPlay($user,true);
            $this->data->scroll[] = $this->getText('{#user_exists_logging_you_in#}',array('text-align' => 'center','margin' => '5 0 20 0'));
            $this->data->onload[] = $this->getCompleteAction();
            return true;
        }

        $this->collectLocation();
        $this->setProfilePic();
        //$this->setProfileComment();

        if($this->menuid == 'reg_continue_4' AND empty($this->errors)){
            $this->data = new stdClass();
            $this->data->scroll[] = $this->getFullPageLoader();
            $this->saveVariables();
            $this->finishUp();
            $this->data->onload[] = $this->getCompleteAction();
            return true;
        }

        $this->data->footer[] = $this->getHairline('#E9E9E9');
        $this->data->footer[] = $this->getTextbutton('{#finish#}',array('style' => 'general_button_style_footer','id' => 'reg_continue_4','submit_menu_id' => 'saver'));

    }

    public function collectName(){
        if ($this->getConfigParam('collect_name',1)) {
            $realname = $this->getVariable('real_name');
            $name = $this->getVariable('name');

            if ($realname AND !$name) {
                $this->saveVariable('name',$realname);
            }

            if($this->menuid == 'reg_continue') {
                $error = $this->checkForError('real_name', '{#please_input_your_real_name#}');
            } else {
                $error = false;
            }

            $this->data->scroll[] = $this->formkitField('real_name','{#name#}','{#input_your_name#}',false,$error);
        }
    }

    public function collectNickname(){
        if($this->menuid == 'reg_continue') {
            $error = $this->checkForError('screen_name', '{#screen_name_already_taken#} / {#not_valid#}');
        } else {
            $error = false;
        }

        $this->data->scroll[] = $this->formkitField('screen_name','{#nickname#}','{#input_your_nickname#}',false,$error);
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

    public function collectInterests(){
        if($this->menuid == 'reg_continue') {
            $error = $this->checkForError('interests', '{#please_choose_at_least_one_interest#}');

            /* saving tags */
            foreach($this->submitvariables as $key=>$val){
                if(stristr($key,'temp_interests_')){
                    $id = str_replace('temp_interests_','',$key);
                    $savearray[$id] = $val;
                    if($val){
                        $error = false;
                    }
                }
            }
        } else {
            $error = false;
        }

        $listparams['variable'] = 'temp_interests';
        $interests = array('pandas'=>'{#pandas#}','tigers'=>'{#tigers#}','flowers' => '{#flowers#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}',$interests,$listparams,$error);
    }


    public function collectApproval(){
        $error = false;

        if($this->menuid == 'reg_continue') {
            if(!$this->getSubmittedVariableByName('terms_accepted')){
                $error = '{#please_approve_the_terms#}';
            }
        }

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam('terms_popup');
        $onclick->open_popup = 1;

        $this->data->scroll[] = $this->formkitCheckbox('terms_accepted','{#approve_the_terms#}',array('onclick' => $onclick),$error);
    }

    public function saveInterests(){
        foreach($this->submitvariables as $key=>$val){
            if(stristr($key,'temp_interests_')){
                $id = str_replace('temp_interests_','',$key);
                $savearray[$id] = $val;
            }
        }

        if(isset($savearray)){
            $this->saveVariable('interests',json_encode($savearray));
        }
    }

    public function setProfilePic(){

            $this->data->scroll[] = $this->getText('{#please_add_a_profile_photo#}', array( 'style' => 'register-text-step-2'));

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
            $img[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'), 'crop' => 'round', 'width' => '150','text-align' => 'center','floating' => "1",'float' => 'center',
                'border-width' => '5', 'border-color' => '#ffffff','border-radius' => '75'));
            $img[] = $this->getText(' ');

            $this->data->scroll[] = $this->getColumn($img,array('text-align' => 'center','height' => '150','margin' => '8 0 8 0','floating' => "1",'float' => 'center'));

            if($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 'sex-woman')){
                $this->data->scroll[] = $this->getText('{#uploading#} ...', array( 'style' => 'uploading_text'));
            }

            $this->data->scroll[] = $this->getTextbutton($txt, array(
                'variable' => $this->vars['profilepic'],
                'action' => 'upload-image',
                'sync_upload'=>true,
                'max_dimensions' => '900',
                'style' => 'general_button_style' ,
                'id' => $this->vars['profilepic']));

            if($this->menuid == 'reg_continue_2' AND $pic == 'filmstrip-placeholder.png'){
                $this->errors = true;
                $this->data->footer[] = $this->getText('{#please_upload_image#}',array('style' => 'register-text-step-error'));
                $this->data->footer[] = $this->getSpacer(8);
            }

        }





    public function setProfileComment() {

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
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#company_description#} ({#required#})', 'style' => 'general_textarea'));
        } else {
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#profile_comment#} ({#required#})', 'style' => 'general_textarea'));
        }

        if($this->menuid == 'reg_continue_2' AND !$commentcontent){
            $this->errors = true;
            $this->data->footer[] = $this->getText('{#please_input_profile_comment#}',array('style' => 'register-text-step-error'));
            $this->data->footer[] = $this->getSpacer(8);
        }


    }


    public function transferMessages(){
        $items = MobilefeedbacktoolModel::model()->findAllByAttributes(array('pending_username' => $this->getSavedVariable('real_name')));
        
        foreach($items as $item){
            $ob = MobilefeedbacktoolModel::model()->findByPk($item->id);
            $ob->pending_username = '';
            $ob->recipient_id = $this->playid;
            $ob->update();
        }
    }



    public function finishUp(){

        $this->initUserlist();
        $this->dataobj->invalidateCache();
        $this->transferMessages();

        if(!$this->getSavedVariable('gender')){
            $this->saveVariable('gender','man');
        }

        $this->updateLocalRegVars();
        $this->beforeFinishRegistration();

        $this->saveVariable('logged_in',1);
        $this->saveVariable('look_for_men',1);
        $this->saveVariable('look_for_women',1);
        $this->saveVariable('men',1);
        $this->saveVariable('women',1);

        if ( !$this->getConfigParam('require_match_entry') ) {
            return false;
        }

        $this->initMobileMatching();
        $this->mobilematchingobj->turnUserToItem(false,__FILE__);

        Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
        MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);

    }

    public function updateLocalRegVars() {

        // If user already completed the registration process
        if ( $this->getSavedVariable( 'reg_phase' ) == 'complete' ) {
            return false;
        }

        $this->loadVariableContent( true );

        $gender = $this->getSavedVariable('gender');

        if ( $gender == 'man' ) {
            $this->saveVariable( 'men', 0 );
            $this->saveVariable( 'women', 1 );
        } else if ( $gender == 'woman' ) {
            $this->saveVariable( 'men', 1 );
            $this->saveVariable( 'women', 0 );
        }

        // $this->saveVariable('logged_in', 1);
        $this->saveVariable('notify', 1);
    }
}