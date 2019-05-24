<?php

class matchswappMobileregister2SubController extends Mobileregister2Controller {

    public $step_one_skipped = false;

	public function matchswappPhase1(){

        // User already Signed In with Facebook, so go to Step 2 instead
        if ( isset($this->varcontent['fb_token']) AND !empty($this->varcontent['fb_token']) ) {
            $this->saveVariable('reg_phase', 2);
            $this->step_one_skipped = true;
            $this->matchswappPhase2();
            return true;
        }

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('reg-logo.png') ) {
            $image_file = 'reg-logo.png';
        }

        if(isset($image_file)){
            $this->data->scroll[] = $this->getImage( $image_file );
        }

        if($this->getSavedVariable('password') AND $this->menuid != 'mobilereg_do_registration'){

            if($this->menuid == 'create-new-user'){
                Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
                $loginmodel = new MobileloginModel();
                $loginmodel->userid = $this->userid;
                $loginmodel->playid = $this->playid;
                $loginmodel->gid = $this->gid;
                $play = $loginmodel->newPlay();
                $this->playid = $play;

                $this->data->scroll[] = $this->getText('{#creating_new_account#}', array( 'style' => 'register-text-step-2'));

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
                return true;
            } else {
                $this->data->scroll[] = $this->getSpacer('15');
                $this->data->scroll[] = $this->getText('{#are_you_sure#}', array( 'style' => 'register-text-step-2'));
                $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                    'style' => 'register-text-step-2',
                    'id' => 'back',
                    'action' => 'open-branch',
                    'config' => $this->getConfigParam('login_branch'),
                ));

                $this->data->scroll[] = $this->getSpacer('15');
                $buttonparams2 = new StdClass();
                $buttonparams2->action = 'submit-form-content';
                $buttonparams2->id = 'create-new-user';
                $this->data->footer[] = $this->getText('{#create_a_new_account#}',array('style' => 'general_button_style_footer','onclick' => $buttonparams2));
                return true;
            }
        }

        $this->saveVariable('reg_phase',1);
        $this->data->scroll[] = $this->getSpacer('15');

        if($this->fblogin === false) {
            if ($this->getConfigParam('login_branch')) {
                $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                    'style' => 'register-text-step-2',
                    'id' => 'back',
                    'action' => 'open-branch',
                    'config' => $this->getConfigParam('login_branch'),
                ));
            }
        }

        $this->data->scroll[] = $this->getSpacer('10');
        $regfields = $this->setRegFields();

        if($regfields === true){
            $this->saveRegData();
            $this->matchswappPhase2();
            return true;
        } else {
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getTextbutton('{#register#}',array('style' => 'general_button_style_footer','id' => 'mobilereg_do_registration','submit_menu_id' => 'saver'));
        }

        return true;
    }
    

    public function matchswappPhase2(){

        if(isset($this->menuid) AND $this->menuid == 'continue_to_3'){
            $this->matchswappPhase3();
            return true;
        }

        $cache = Appcaching::getGlobalCache('location-asked'.$this->playid);

        if(!$cache AND $this->getConfigParam('ask_for_location')){
            $buttonparams = new StdClass();
            $buttonparams->action = 'submit-form-content';
            $buttonparams->id = 'save-variables';
            $this->data->onload[] = $buttonparams;

            $menu2 = new StdClass();
            $menu2->action = 'ask-location';
            $this->data->onload[] = $menu2;
            Appcaching::setGlobalCache('location-asked'.$this->playid,true);
        }

        // $this->data->footer[] = $this->getText($this->getSavedVariable('lat'));

        if ( !$this->step_one_skipped ) {
            $this->saveVariables();
            $this->loadVariableContent(true);
        }

        $this->closeLogin();
        $this->setProfilePic();
        $this->setSex();
        $this->setPreferences();
        $this->setProfileComment();
        $this->setTerms();

        $location = $this->getConfigParam('ask_for_location');
        $lat = $this->getSavedVariable('lat');
        $terms = $this->getSavedVariable('terms_accepted');
        $profilepic = $this->getSavedVariable('profilepic');
        $profilecomment = $this->getSavedVariable('profile_comment');

        $this->data->footer[] = $this->getHairline('#ffffff');

        if(!$lat AND $cache AND $location){
            $this->geolocateMe();
            $error = true;
            $locationerror = true;
        }

        if($this->menuid == 'saver-2'){
            $error = false;

            if(!$profilepic){
                $this->data->footer[] = $this->getText('{#please_add_a_profile_pic#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            /*
            if(!$profilecomment){
                $this->data->footer[] = $this->getText('{#please_add_a_profile_description#}',array('style' => 'register-text-step-2'));
                $error = true;
            }
            */

            if(!$terms){
                $this->data->footer[] = $this->getText('{#please_accept_the_terms#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if($error == false){
                $this->data->scroll = array();
                $this->data->scroll[] = $this->getSpacer('100');
                $this->data->scroll[] = $this->getText('{#loading_matches#}...',array('style' => 'register-text-step-2'));
                $this->data->footer = array();
                $this->finishUp();
                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;

                    //$this->getTextbutton('This will complete',array('onclick' => $complete,'id' => 'whatver'));
                return true;
            }
        }

        /* if there is location error, we will provide a locate button instead of this */
        if(!isset($locationerror)){
            $this->data->footer[] = $this->getTextbutton('{#continue#}',array('style' => 'general_button_style_footer','id' => 'saver-2'));
        }

        $this->data->scroll[] = $this->getSpacer(40);
        Appcaching::setGlobalCache( $this->playid .'-' .'registration',true);
        return true;
    }


    public function finishUp(){

        if(!$this->getSavedVariable('gender')){
            $this->saveVariable('gender','man');
        }

        $this->updateLocalRegVars();

        $this->beforeFinishRegistration();

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

    public function matchswappPhase3(){

        $this->saveVariables();

        $terms = $this->getSavedVariable('approve_terms');
        $lat = $this->getSavedVariable('lat');

        $this->saveVariable('reg_phase',3);
        $lon = $this->getSavedVariable('lon');

        $btnaction1 = new StdClass();
        $btnaction1->action = 'submit-form-content';
        $btnaction1->id = 'save-variables';

        $btnaction2 = new StdClass();
        $btnaction2->action = 'ask-location';
        $btnaction2->sync_upload = false;

        $buttonparams['onclick'] = array($btnaction1,$btnaction2);

        $buttonparams['style'] = 'general_button_style_footer';
        $buttonparams['id'] = 'dolocate';
        $this->data->scroll[] = $this->getSpacer(200);

        if(!$lat){
            if(isset($this->menuid) AND $this->menuid == 'dolocate'){
                $this->data->scroll[] = $this->getText('Something went wrong with the location.', array( 'style' => 'register-text-step-2'));
            } else {
                $this->data->scroll[] = $this->getText('Registration requires your location information. Otherwise the app won\'t work.', array( 'style' => 'register-text-step-2'));
            }
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getText('Locate me now to continue', $buttonparams);
        } else {
            /* we do this only if the matching action is present within the app */

            $this->data->scroll[] = $this->getText('How wonderful, all required information is now set up. Please be respectful.', array( 'style' => 'register-text-step-2'));
            $this->generalComplete();
        }
    }


    public function setRegFields(){
        $error = false;
        $error2 = false;
        $error3 = false;
        $error4 = false;
        $error5 = false;
        $error6 = false;

        if ($this->fblogin == false AND $this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getFacebookSignInButton('fb-login');

            if ($this->getConfigParam('collect_name',1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name',$realname);
                }

                $error = $this->checkForError('real_name','{#please_input_first_and_last_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png',$this->vars['real_name'],'{#name#}',$error);
            }
        } elseif ($this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getText("{#you_are_connected_with_facebook#}",array('style' => 'register-text-step-2'));
        } else {
            if ($this->getConfigParam('collect_name',1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name',$realname);
                }

                $error2 = $this->checkForError('real_name','{#please_input_your_real_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png',$this->vars['real_name'],'{#name#}',$error2);
            }
        }

        // $this->data->scroll[] = $this->getFieldWithIcon('icon-surname.png',$this->vars['surname'],'Surname',false,'text');

        if ($this->getConfigParam('show_email',1)) {
            $error3 = $this->checkForError('email','{#input_valid_email#}','{#email_already_exists#}');
            $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png',$this->getVariableId('email'),'{#email#}',$error3);
        }

        if ($this->getConfigParam('collect_phone')) {
            $error4 = $this->checkForError('phone','{#please_input_a_valid_phone#}');
            $this->data->scroll[] = $this->getFieldWithIcon('phone-icon-register.png',$this->vars['phone'],'{#phone#} ({#with_country_code#}',$error4);
        }

        if ( $this->getConfigParam('collect_address') ) {
            $this->data->scroll[] = $this->getFieldWithIcon('icon-address.png',$this->vars['address'],'{#address#}',false,'text');
        }

        if ($this->getConfigParam('collect_password')) {
            $error5 = $this->checkForError('password_validity','{#at_least#} 4 {#characters#}');
            $error6 = $this->checkForError('password_match','{#passwords_do_not_match#}');
            $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->fields['password1'],'{#password#}',$error5,'password');
            $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->fields['password2'],'{#password_again#}',$error6,'password','mobilereg_do_registration');
        }

        $this->data->scroll[] = $this->getSpacer('5');

        if (!$error AND !$error2 AND !$error3 AND !$error4 AND !$error5 AND !$error6 AND $this->menuid == 'mobilereg_do_registration') {
            $this->saveVariable('reg_phase',2);
            unset($this->data->scroll);
            $this->data->scroll = array();
            // Need to investigate why ..
            unset($this->data->footer);
            return true;
        }

        return false;
    }
    
}