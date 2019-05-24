<?php

class fashionMobileregister2SubController extends Mobileregister2Controller {

    /** @var MobileloginModel */
    public $loginmodel;

    public function fashionPhase1(){
        $error = false;

        if($this->menuid == 'show-loader'){
            $this->data->scroll[] = $this->getSpacer('40');
            $this->data->scroll[] = $this->getLoader('',array('color' => '#000000'));
            $this->data->scroll[] = $this->getText('{#loading#}',array('style' => 'register-text-step-2'));
            $this->data->footer[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel'));
            return $this->data;
        }

        /* handling case where user already exists */
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

        if($this->menuid == 'set-role' OR $this->getSavedVariable('role')){
            if($this->getSavedVariable('instagram_temp_token') AND !$this->getSavedVariable('instagram_token')){
                $this->handleInstagramConnect();
            }

            /* save the role or give error */
            if(!$this->getSubmittedVariableByName('role')){
                $error = '(#please_choose_your_role#}';
            } else {
                $this->saveVariable('role',$this->getSubmittedVariableByName('role'));
            }

            if($this->getSavedVariable('instagram_username') OR $this->getSavedVariable('role') == 'brand'){
                if($this->getSavedVariable('role') == 'brand'){
                    $this->saveVariable('user_approved',1);
                } else {
                    $this->saveVariable('user_approved',0);
                }
                $this->saveVariable('reg_phase',2);
                $this->fashionPhase2();
                return true;
            }

            if(!$this->getSavedVariable('instagram_username') AND $this->getSavedVariable('role') == 'influencer'){
                /* in this case we need to close the login, otherwise after completing instagram login user would end up on the login screen */
                $this->data->header[] = $this->getText('{#connect_instagram#}', array( 'style' => 'fashion-register-message' ));
                $this->data->scroll[] = $this->getText('{#instagram_connect_for_influencers_mandatory#}', array( 'style' => 'register-text-step-roles'));
                $this->data->footer[] = $this->getSpacer('15');
                $this->data->footer[] = $this->getInstagramSignInButton($this->getConfigParam('instagram_login_provider'));
                $this->data->footer[] = $this->getSpacer('35');
                return true;
            }
        }

        $this->setRole($error);
    }




    public function handleInstagramConnect(){

        $this->initLoginModel();

        $apikey = Aemobile::getConfigParam($this->gid,'instagram_apikey');
        $apisecret = Aemobile::getConfigParam($this->gid,'instagram_secretkey');

        if(strlen($this->getSavedVariable('instagram_temp_token') < 5)){
            sleep(1);
            $this->loadVariableContent(true);
        }

        if(strlen($this->getSavedVariable('instagram_temp_token') < 5)) {
            return false;
        }

        $insta = new InstagramConnector($apikey,$apisecret,$this->getSavedVariable('instagram_temp_token'));
        $self = $insta->get('users/self');

        if(isset($self->data)){
            $this->loginmodel->addInstagramInfoToUser($self,$this->getSavedVariable('instagram_temp_token'));
        }

        $this->loadVariables();

    }

    public function fashionPhase2(){

        // Failsafe
        if ( $this->getSavedVariable('role') == 'influencer' AND !$this->getSavedVariable('instagram_username') ) {
            $this->saveVariable( 'reg_phase', 1 );
            $this->saveVariable( 'role', 'influencer' );
            $this->fashionPhase1();
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

        $this->saveVariable('reg_phase',2);
        $this->setBackButton();

        $this->data->scroll[] = $this->getSpacer('10');
        $regfields = $this->setRegFields();

        if($regfields === true){
            $this->closeLogin();
            $this->saveRegData();
            $this->saveVariable('reg_phase',3);
            $this->fashionPhase3();
            return true;
        } else {
            $onclicks[] = $this->getOnclick('location');
            $onclicks[] = $this->getOnclick('id',false,'mobilereg_do_registration');
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getText('{#register#}',array('style' => 'general_button_style_footer','onclick' => $onclicks,'submit_menu_id' => 'saver'));
        }

        return true;
    }

    public function setProfilePic(){

        if($this->getConfigParam('require_photo')){
            $this->data->scroll[] = $this->getText('{#please_add_profile_pic#}', array( 'style' => 'register-text-step-2'));

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
                'border-width' => '5', 'border-color' => '#ffffff','border-radius' => '75','priority' => 9));
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
        }
    }

    public function setRole($error=false){

        $this->data->header[] = $this->getText('{#choose_your_role#}', array( 'style' => 'fashion-register-message' ));

        $selectstate_active = array('variable_value' => "influencer",'style' => 'selector_role_selected','allow_unselect' => 1,'animation' => 'fade');
        $selectstate_w = array('variable_value' => "brand",'style' => 'selector_role_selected','allow_unselect' => 1,'animation' => 'fade');

        if($error){
            $this->data->footer[] = $this->getText($error,array('style' => 'register-text-step-error'));
        }

        $col[] = $this->getText('{#influencer#}',array('variable' => 'role','selected_state' => $selectstate_active,'style' => 'selector_role'));
        $col[] = $this->getVerticalSpacer('10%');
        $col[] = $this->getText('{#brand#}',array('variable' => 'role','selected_state' => $selectstate_w,'style' => 'selector_role'));

        $this->data->footer[] = $this->getRow($col,array('margin' => '14 40 31 40','text-align' => 'center'));
        $this->data->footer[] = $this->getTextbutton('{#continue#}',array('id' => 'set-role'));
        $this->data->scroll[] = $this->getText('{#role_long_explanation#}', array( 'style' => 'register-text-step-roles'));

    }

    public function fashionPhase3(){
        if(isset($this->menuid) AND $this->menuid == 'continue_to_4'){
            $this->fashionPhase4();
            return true;
        }

        $cache = Appcaching::getGlobalCache('location-asked'.$this->playid);

        if(!$cache AND !$this->getSavedVariable('lat')){
            $buttonparams = new StdClass();
            $buttonparams->action = 'submit-form-content';
            $buttonparams->id = 'save-variables';
            $this->data->onload[] = $buttonparams;

            $menu2 = new StdClass();
            $menu2->action = 'ask-location';
            $this->data->onload[] = $menu2;
            Appcaching::setGlobalCache('location-asked'.$this->playid,true);
        }

        //$this->data->footer[] = $this->getText($this->getSavedVariable('lat'));

        if(isset($this->submitvariables['website'])){
            $this->submitvariables['website'] = strtolower($this->submitvariables['website']);
        }

        $this->saveVariables();
        $this->loadVariableContent(true);
        //$this->closeLogin();
        $this->setProfilePic();

        $val = $this->getSavedVariable('website') ? $this->getSavedVariable('website') : '';

        if($this->getSavedVariable('role') == 'brand') {
            $this->data->scroll[] = $this->getFieldtext($val, array('style' => 'general_button_style_black', 'variable' => 'website', 'hint' => '{#website#}',
                'value' => $val));
        }

        $this->setPreferences();
        $this->setProfileComment();
        $this->setTerms();

        $location = $this->getConfigParam('ask_for_location');
        $lat = $this->getSavedVariable('lat');
        $terms = $this->getSavedVariable('terms_accepted');
        $profilepic = $this->getSavedVariable('profilepic');
        $profilecomment = $this->getSavedVariable('profile_comment');
        $website = $this->getSavedVariable('website');

        $this->data->footer[] = $this->getHairline('#ffffff');

        if(!$lat AND $cache){
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

            if(!$profilecomment){
                if($this->getSavedVariable('role') == 'brand'){
                    $this->data->footer[] = $this->getText('{#please_add_a_company_description#}',array('style' => 'register-text-step-2'));
                } else {
                    $this->data->footer[] = $this->getText('{#please_add_a_profile_description#}',array('style' => 'register-text-step-2'));
                }
                $error = true;
            }

            if(!$this->validateWebsite($website) AND $this->getSavedVariable('role') == 'brand'){
                $this->data->footer[] = $this->getText('{#please_input_a_valid_website#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

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



    public function setProfileComment() {
        if ( !$this->getConfigParam('require_comment') ) {
            return false;
        }

        if (isset($this->varcontent['profile_comment'])) {
            $commentcontent = $this->varcontent['profile_comment'];
        } elseif($this->getSavedVariable('instagram_bio')) {
            $commentcontent = $this->getSavedVariable('instagram_bio');
        } else {
            $commentcontent = '';
        }

        if($this->getSavedVariable('role') == 'brand') {
            $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('company'),array('style' => 'general_button_style_black','variable' => 'company', 'hint' => '{#company_name#}',
                'value' => $this->getSubmittedVariableByName('company')));
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#company_description#} ({#required#})', 'style' => 'general_textarea'));
        } else {
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#comment#} ({#required#})', 'style' => 'general_textarea'));
        }

    }

    public function finishUp(){

        if(!$this->getSavedVariable('gender')){
            $this->saveVariable('gender','man');
        }
        
        $this->saveVariable('reg_phase','complete');

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

    public function fashionPhase4(){

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


        if($this->getSavedVariable('instagram_username')){

            $this->data->scroll[] = $this->getText("{#you_are_connected_with_instagram#}",array('style' => 'register-text-step-2'));
            if ($this->getConfigParam('collect_name',1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name',$realname);
                }

                $error = $this->checkForError('real_name','{#please_input_first_and_last_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png',$this->vars['real_name'],'{#name#}',$error);
            }

        } elseif ($this->fblogin == false AND $this->getConfigParam('facebook_enabled')) {
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

                if(!$this->getSubmittedVariableByName('real_name')){
                    $error2 = '{#please_input_name#}';
                }

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