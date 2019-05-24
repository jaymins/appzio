<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class Mobileregister2Controller extends ArticleController {

    public $data;
    public $configobj;
    public $theme;
    public $phase;

    /* marks whether we are using some form of social authentication */
    public $social_authentication = false;

    public $fields = array('password1' => 'password1', 'password2' => 'password2');
    public $error;
    public $no_spacer = false;

    public function tab1(){
        $this->theme = ( isset($this->configobj->article_action_theme) ? $this->configobj->article_action_theme : '' );
        $this->data = new StdClass();
        $this->phase = $this->getSavedVariable('reg_phase') ? $this->getSavedVariable('reg_phase') : 1;

        if($this->menuid == 'save-variables'){
            $this->saveVariables();
        }

        if($this->fblogin){
            $this->social_authentication = true;
        }

        if ($this->theme) {
            $function = $this->theme . 'Phase' . $this->phase;
        } else {
            return $this->oldTab1();
        }

        if (method_exists($this,$function)) {
            $this->$function();
        } elseif($this->phase == 'complete') {
            $this->generalPhaseComplete();
        } else {
            return $this->oldTab1();
        }

        return $this->data;
    }

    public function tab2(){
        $this->theme = ( isset($this->configobj->article_action_theme) ? $this->configobj->article_action_theme : '' );
        $this->data = new stdClass();
        $this->data->scroll[] = $this->getText('');


        if(!$this->getConfigParam('collect_phone')){
            return $this->data;
        }

        $countrycodes = $this->getCountryCodes();

        if ( empty($countrycodes) ) {
            return $this->data;
        }

        $this->data->scroll[] = $this->getSpacer('5');

        foreach ($countrycodes as $key=>$countrycode){
            $onclick = new stdClass();
            $onclick->action = 'open-tab';
            $onclick->action_config = '1';

            $onclick2 = new stdClass();
            $onclick2->action = 'submit-form-content';
            $onclick2->id = 'countryselected_'.$key;
            $this->data->scroll[] = $this->getText($key .' ('.$countrycode.')',array('style' => 'countrycodeitem','onclick' => array($onclick,$onclick2)));
        }

        $this->data->footer[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel','action' => 'open-tab','config' => 1));
        return $this->data;
    }

    public function generalPhaseComplete(){
        if($this->menuid == 'create-new-user') {
            Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
            $loginmodel = new MobileloginModel();
            $loginmodel->userid = $this->userid;
            $loginmodel->playid = $this->playid;
            $loginmodel->gid = $this->gid;
            $play = $loginmodel->newPlay();
            $this->playid = $play;

            $this->data->scroll[] = $this->getText('{#creating_new_account#}', array('style' => 'register-text-step-2'));
            $complete = new StdClass();
            $complete->action = 'complete-action';
            $this->data->onload[] = $complete;
            return true;
        } else {
            if($this->no_spacer == false){
                $this->data->scroll[] = $this->getSpacer('15');
            }

            if ( $this->getConfigParam( 'actionimage1' ) ) {
                $image_file = $this->getConfigParam( 'actionimage1' );
                $this->data->scroll[] = $this->getImage($image_file);
            }

            $this->data->scroll[] = $this->getSpacer('15');
            $this->data->scroll[] = $this->getText('{#are_you_sure_you_want_to_create_a_new_account#}?', array( 'style' => 'register-text-step-2'));
            $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                'style' => 'register-text-step-2',
                'id' => 'backer',
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

    /* yes, really, we have such function here*/
    public function oldTab1(){

        $this->theme = ( isset($this->configobj->article_action_theme) ? $this->configobj->article_action_theme : '' );

        $this->data = new StdClass();

        if(isset($this->varcontent['real_name']) AND $this->varcontent['real_name'] AND strlen($this->varcontent['real_name']) > 3){
            $this->data->scroll = $this->finaliseRegistration(array());
        } elseif($this->menuid == 'mobilereg_do_registration'){
            $this->data->scroll = $this->getMainScroll($validate = true);
        } else {
            $this->data->scroll = $this->getMainScroll($validate = false);
        }

        return $this->data;
    }

    /* note: all data must be validated by this point */
    public function saveRegData() {
        $pass = $this->getSubmitVariable($this->fields['password1']);
        $this->saveVariables();
        $this->loadVariables();

        if($pass){
            /* save password */
            $this->saveVariable('password',sha1(strtolower(trim($pass))));
        }

        if(isset($this->submitvariables['66666662']) AND $this->submitvariables['66666662']){
            $this->saveVariable('email',$this->submitvariables['66666662']);
        }

        if(isset($this->varcontent['real_name']) AND !isset($this->varcontent['name'])){
            $this->saveVariable('name',$this->varcontent['real_name']);
        }

        if(isset($this->varcontent['name']) AND !isset($this->varcontent['real_name'])){
            $this->saveVariable('real_name',$this->varcontent['name']);
        }

        if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']) {
            $this->saveVariable('profilepic',$this->varcontent['fb_image']);
        }

        if(isset($this->varcontent['hcp']) AND $this->varcontent['hcp']) {
            if($this->varcontent['hcp'] > 54){
                $this->saveVariable('hcp',54);
            }
        }
        
        $fbtoken = $this->getSavedVariable('fb_token');
        $usr = UserGroupsUseradmin::model()->findByPk($this->userid);
        $usr->play_id = $this->playid;
        $usr->update();

        if($fbtoken){
            UserGroupsUseradmin::addFbInfo($this->userid,$fbtoken,$this->gid,$this->playid);
            $this->saveVariable('fb_universal_login','0');
        }

    }

    public function setSex() {
        if ( !$this->getConfigParam('require_sex') ) {
            return false;
        }
        
        $current = $this->getSavedVariable( 'gender' );

        switch ($this->menuid) {
            case 'man':
                $class_man = 'radiobutton_selected';
                $class_women = 'radiobutton';
                $this->saveVariable('gender','man');
                break;
            
            case 'woman':
                $class_man = 'radiobutton';
                $class_women = 'radiobutton_selected';
                $this->saveVariable('gender','woman');
                break;

            default:

                if ( $current == 'man' ) {
                    $class_man = 'radiobutton_selected';
                    $class_women = 'radiobutton';
                } else if ( $current == 'woman' ) {
                    $class_man = 'radiobutton';
                    $class_women = 'radiobutton_selected';
                } else {
                    $class_man = 'radiobutton_selected';
                    $class_women = 'radiobutton';
                }

                break;
        }

        $columns[] = $this->getTextbutton('{#man#}', array('id' => 'man', 'style' => $class_man, 'sync_upload' => false));
        $columns[] = $this->getTextbutton('{#woman#}', array('id' => 'woman', 'style' => $class_women, 'sync_upload' => false));

        $this->data->scroll[] = $this->getRow($columns, array('style' => 'general_button_style'));
    }

    public function setTerms(){

        if ( !$this->getConfigParam('require_terms') ) {
            return false;
        }

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = $this->getConfigParam('terms_popup');
        $onclick->config = $this->getConfigParam('terms_popup');
        $onclick->action_config = $this->getConfigParam('terms_popup');
        $onclick->open_popup = '1';

        $terms_label = '{#review_terms_and_conditions#}';

        if ( $this->theme == 'dealsapp' ) {
            $terms_label = '{#i_agree_to_receive_push_notifications_with_updates#}';
        }

        $this->data->scroll[] = $this->getText($terms_label, array('style' => 'general_button_style_black','onclick' => $onclick));
        $columns[] = $this->getFieldonoff($this->getSavedVariable('terms_accepted'), array('margin' => '0 9 0 0', 'variable' => $this->getVariableId('terms_accepted')));

        $columns[] = $this->getText('{#i_approve_terms#}', array('style' => 'terms-hint'));
        $this->data->scroll[] = $this->getRow($columns, array('style' => 'radio_button_container'));
    }

    public function setPreferences() {
        if ( !$this->getConfigParam('require_preferences') ) {
            return false;
        }

        $current = $this->getSavedVariable( 'date_preferences' );
        
        switch ($this->menuid) {
            case 'acceptor':
                $class_acc = 'radiobutton_selected';
                $class_req = 'radiobutton';
                $this->saveVariable('date_preferences','acceptor');
                break;
            
            case 'requestor':
                $class_acc = 'radiobutton';
                $class_req = 'radiobutton_selected';
                $this->saveVariable('date_preferences','requestor');
                break;

            default:

                if ( $current == 'acceptor' ) {
                    $class_acc = 'radiobutton_selected';
                    $class_req = 'radiobutton';
                } else if ( $current == 'requestor' ) {
                    $class_acc = 'radiobutton';
                    $class_req = 'radiobutton_selected';
                } else {
                    $class_acc = 'radiobutton';
                    $class_req = 'radiobutton_selected';
                }

                break;
        }

        $columns[] = $this->getTextbutton('{#planner#}', array('id' => 'requestor', 'style' => $class_req, 'sync_upload' => false));
        $columns[] = $this->getTextbutton('{#acceptor#}', array('id' => 'acceptor', 'style' => $class_acc, 'sync_upload' => false));

        $this->data->scroll[] = $this->getRow($columns, array('style' => 'general_button_style'));
    }

    public function setProfileComment() {
        if ( !$this->getConfigParam('require_comment') ) {
            return false;
        }

        if (isset($this->varcontent['profile_comment'])) {
            $commentcontent = $this->varcontent['profile_comment'];
        } else {
            $commentcontent = '';
        }

        $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#comment#} ({#required#})', 'style' => 'general_textarea'));
    }

    public function setProfilePic(){

        $onclick = new stdClass();
        $onclick->action = 'upload-image';
        $onclick->sync_upload = true;
        $onclick->max_dimensions = '900';
        $onclick->variable = $this->vars['profilepic'];

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
                'border-width' => '5', 'border-color' => '#ffffff','border-radius' => '75','onclick' => $onclick,'priority' => 9));
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

    public function setAge() {
        $age_var_id = $this->getVariableId( 'age' );

        if ( $this->getVariable( 'age' ) AND $this->getVariable( 'age' ) > 0 AND is_numeric($this->getVariable( 'age' ))) {
            return false;
        }

        $this->data->scroll[] = $this->getFieldtext('', array(
            'style' => 'general_textfield',
            'input_type' => 'number',
            'hint' => '{#enter_your_age#}',
            'variable' => $age_var_id
        ));
    }

    public function generalComplete(){
        $this->beforeFinishRegistration();

        $menu = new StdClass();
        $menu->action = 'list-branches';

        $this->data->onload[] = $menu;
        $this->data->footer[] = $this->getHairline('#ffffff');
        $this->data->footer[] = $this->getTextbutton('{#all_set_complete_registration#}', array('submit_menu_id' => 'saver','action' => 'complete-action', 'style' => 'general_button_style_footer' ,'id' => 'submitter'));
    }

    public function setRegFields(){
        $error = false;
        $error2 = false;
        $error3 = false;
        $error4 = false;
        $error5 = false;
        $error6 = false;
        $error7 = false;

        if ($this->fblogin == false AND $this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getFacebookSignInButton('fb-login');

            $this->collectNameSub();
        } elseif ($this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getText("{#facebook_connected_pls_register#}",array('style' => 'register-text-step-2'));
        } else {
            $this->collectNameSub();
        }

        if ( $this->fblogin ) {
            $this->data->scroll[] = $this->getText($this->fblogin);
        }

        if(!$this->social_authentication OR !$this->getConfigParam('simple_social')){
            if ($this->getConfigParam('show_email',1) AND !$this->getSavedVariable('email')) {
                $error3 = $this->checkForError('email','{#input_valid_email#}');
                $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png',$this->getVariableId('email'),'{#email#}',$error3);
            }
        }

        if ($this->getConfigParam('collect_phone')) {
            $error4 = $this->checkForError('phone','{#input_valid_phone#}');
            $this->data->scroll[] = $this->getFieldWithIcon('phone-icon-register.png',$this->getVariableId('phone'),'{#phone#} ({#with_a_country_code#})',$error4);
        }

        if ( $this->getConfigParam('collect_address') ) {
            $this->data->scroll[] = $this->getFieldWithIcon('icon-address.png',$this->getVariableId('address'),'{#address#}',false,'text');
        }

        if(!$this->social_authentication OR !$this->getConfigParam('simple_social')) {
            if ($this->getConfigParam('collect_password')) {
                $error5 = $this->checkForError('password_validity', '4 {#chars_at_least#}');
                $error6 = $this->checkForError('password_match', 'Passwords don\'t match');
                $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png', $this->fields['password1'], '{#password#} (4 {#chars_at_least#})', $error5, 'password');
                $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png', $this->fields['password2'], '{#repeat_password#}', $error6, 'password', 'mobilereg_do_registration');
            }
        }

        if ( $this->getConfigParam('require_terms') ) {
            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->id = $this->getConfigParam('terms_popup');
            $onclick->config = $this->getConfigParam('terms_popup');
            $onclick->action_config = $this->getConfigParam('terms_popup');
            $onclick->open_popup = '1';
            $this->data->scroll[] = $this->getText('{#view_terms#}', array('style' => 'general-button','onclick' => $onclick));

            $error7 = $this->checkForError('agree_terms','{#please_agree_with_privacy_policy#}');
            $this->data->scroll[] = $this->getCheckbox('agree_terms', '{#i_agree_with_the_privacy_policy_and_the_notification_rules#}', $error7, array( 'width' => '80%' ));
        }

        $this->data->scroll[] = $this->getSpacer('5');

        if (!$error AND !$error2 AND !$error3 AND !$error4 AND !$error5 AND !$error6 AND !$error7 AND  $this->menuid == 'mobilereg_do_registration') {
            $this->saveVariable('reg_phase',2);
            // Need to investigate why ..
            unset($this->data->footer);
            return true;
        }

        return false;
    }

    public function getMainScroll($validate=false){

        $output = array();

        $output[] = $this->getMenu('create_profile_why',array('style'=>'register_menu'));
        $output[] = $this->getMenu('mobilereg_connect_facebook',array('style'=>'register_menu'));
        $output[] = $this->getImage('connect-or.png', array( 'style' => 'chat-divider'));

        if($validate == true){
            $valid = true;
            $out2 = $output;

            if(str_word_count($this->submitvariables['66666661']) > 1){
                $out2 = $this->getFieldReg($out2,'66666661','{#name#}');
            } else {
                $out2 = $this->getErrorField($out2,'66666661','{#please_input_first_and_lastname#}');
                $valid = false;
            }

            if ( isset($this->configobj->show_email) AND !empty($this->configobj->show_email) ) {
                $validator = new CEmailValidator;
                $validator->checkMX = true;
                if(!$validator->validateValue($this->submitvariables['66666662'])){
                    $out2 = $this->getErrorField($out2,'66666662','{#please_use_a_working_email#}');
                    $valid = false;
                } else {
                    $out2 = $this->getFieldReg($out2,'66666662','{#email#}');
                }
            }

            if(!strstr($this->submitvariables['66666663'],'+')){
                $out2 = $this->getErrorField($out2,'66666663','{#include_plus_in_country-code#}');
                $valid = false;
            } else {
                $out2 = $this->getFieldReg($out2,'66666663','{#phone#}');
            }

            if($valid == false){
                $output = $output+$out2;
            } else {
                $this->data->footer = $this->getFooter( 'step-2' );
                return $this->finaliseRegistration(array());
            }

        } else {
            $output = $this->getFieldReg($output,'66666661','{#full_name#}');

            if($this->getConfigParam('show_email')){
                $output = $this->getFieldReg($output,'66666662','{#email#}');
            }

            $output = $this->getFieldReg($output,'66666663','{#phone#} (+359123456789)');
        }

        $this->data->footer = $this->getFooter( 'step-1' );

        return $output;
    }


    public function closeLogin($dologin=true){
        if ($this->getConfigParam('require_login') == 1) {
            return true;
        }

        $branch = $this->getConfigParam('login_branch');

        if($dologin){
            $this->saveVariable('logged_in',1);
        }

        if(!$branch){
            return false;
        }

        AeplayBranch::closeBranch($branch,$this->playid);
        return true;
    }


    public function finaliseRegistration($output){

        $this->loadVariables();
        $this->saveVariablesInternal();

        if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
            $pic = $this->varcontent['fb_image'];
            $txt = 'Change the photo';

            if(isset($this->varcontent['real_name'])){
                AeplayVariable::updateWithId($this->playid,$this->vars['name'],$this->varcontent['real_name']);
            }
        } elseif(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
            $pic = $this->varcontent['profilepic'];
            $txt = 'Change the photo';
        } else {
            $pic = 'photo-placeholder.png';
            $txt = 'Add a photo';
        }

        $output[] = $this->getImage($pic, array( 'variable' => $this->vars['profilepic'] ,'priority' => 9));
        $output[] = $this->getText('Please finish your profile setup', array( 'style' => 'register-text-step-2' ));
        $output[] = $this->getFieldupload($txt, array( 'type' => 'image', 'variable' => 'profilepic', 'style' => 'register_button' ));
        $output[] = $this->getFieldtextarea('', array( 'variable' => 'profile_comment', 'hint' => 'Comment (optional):', 'style' => 'comment-field','submit_menu_id' => 'continue_to_3' ));

        $this->data->footer = $this->getFooter( 'step-2' );

        return $output;
    }

    public function getFooter( $step ) {
        $output = array();

        switch ($step) {
            case 'step-1':
                $output[] = $this->getTextbutton( 'Sign Up', array('id' => 'mobilereg_do_registration', 'style' => 'register-button' ) );
                break;
            
            case 'step-2':
                $output[] = $this->getTextbutton( 'Sign Up', array('id' => 'mobilereg_finish_registration', 'style' => 'register-button', 'action' => 'complete-action' ) );
                break;
        }

        return $output;
    }


    public function checkForError($field,$msg,$secondarymsg=false){
        
        /* validate only if user clicked on register */
        if ( $this->menuid != 'mobilereg_do_registration' AND !$this->getSavedVariable('lat')) {
            return false;
        }

        /* submitted value */
        $varid = $this->getVariableId($field);
        $value = $this->getVariable($varid);

        $result2 = true;

        switch($field){
            case 'email':

                if(!isset($this->vars['email'])){
                    $result2 = true;
                } else {
                    $result = $this->validateEmail($value);
                    $emailvar = $this->vars['email'];
                    $value  = strtolower( rtrim($value) );
                    $obj = @AeplayVariable::model()->findByAttributes(array('variable_id' => $emailvar,'value' => $value));
                    
                    if(is_object($obj) AND $obj->play_id != $this->playid){
                        $var = $this->getVariableId('reg_phase');
                        $check = @AeplayVariable::model()->findByAttributes(array('play_id' => $obj->play_id,'variable_id' => $var,'value' => 'complete'));

                        if(is_object($check)){
                            $result2 = false;
                        } else {
                            $result2 = true;
                        }
                    } else {
                        $result2 = true;
                    }
                }
                break;

            case 'phone':

                if(strlen($value) < 4){
                    $result = false;
                } elseif(is_numeric($value)){
                    $result = true;
                } elseif(stristr($value,'+')){
                    $result = true;
                } else {
                    $result = false;
                }
                
                break;


            case 'screen_name':
                if($this->getSubmittedVariableByName('screen_name')) {
                    $varid = $this->getVariableId('screen_name');
                    $ob = AeplayVariable::model()->findByAttributes(array('variable_id' => $varid,'value' => $this->getSubmittedVariableByName('screen_name')));

                    if(is_object($ob) AND isset($ob->id)){
                        $result = false;
                    } else {
                        $result = true;
                    }
                } else {
                    $result = false;
                }

                break;

            case 'phone_empty':
                $result = empty($value) ? false : true;
                break;

            case 'real_name':
                $result = $this->checkName( $value );
                break;

            case 'password_match':
                $value1 = $this->getVariable($this->fields['password1']);
                $value2 = $this->getVariable($this->fields['password2']);
                $result = ( $value1 == $value2 ? true : false );
                break;

            case 'password_validity':
                $value1 = $this->getVariable($this->fields['password1']);
                $result = ( strlen($value1) > 3 ? true : false );
                break;

            case 'agree_terms':
                $result = ( $value == 1 ? true : false );
                break;

            case 'string_set':
                $result = strlen($value) > 1 ? true : false;
                break;

            case 'gender':
                if($this->getSubmittedVariableByName('gender') == 'male' OR $this->getSubmittedVariableByName('gender') == 'female'){
                    $result = true;
                } else {
                    $result = false;
                }
                break;

            default:
                $result = false;
                break;
        }


        if( $result2 === false){
            $this->error = true;
            return $secondarymsg;
        }

        if ( isset($result) AND $result === false ) {
            return $msg;
        }

        return false;
    }

    public function validateEmail($email)
    {
        if ( empty($email) ) {
            return false;
        }

        $validator = new CEmailValidator;
        $validator->checkMX = true;

        $email = rtrim( $email );

        if ($validator->validateValue($email)) {
            return true;
        }

        return false;
    }

    public function validateName($value){
        $result = $this->universalWordCount($value) > 0 ? true : false ;
        return $result;
    }

    public function universalWordCount($str) {
        $new_str = str_replace("\xC2\xAD",'', $str);        // soft hyphen encoded in UTF-8
        return preg_match_all('~[\p{L}\'\-]+~u', $new_str); // regex expecting UTF-8
    }

    public function saveVariablesInternal() {
        $variables = array(
            'name' => 66666661,
            'email' => 66666662,
            'phone' => 66666663,
        );

        $data = array();

        foreach ($variables as $key => $var) {
            if ( isset($this->submitvariables[$var]) AND !empty($this->submitvariables[$var]) ) {
                    $data[$key] = $this->submitvariables[$var];
            }
        }

        // Set the registration_phase for the Hairdresser - temporary
        if ( $this->theme == 'hairdresser' ) {
            $data['registration_phase'] = 'booking-1';
        }
    
        AeplayVariable::saveVariablesArray($data,$this->playid,$this->gid,'normal');
    }

    public function getFieldReg($output,$id,$hint){
        $output[] = $this->getFieldtext($this->getVariable($id), array( 'variable' => $id, 'hint' => $hint, 'style' => 'register-field' ));
        return $output;
    }

    public function getErrorField($output,$id,$msg){
        $output[] = $this->getFieldtext($this->getVariable($id), array( 'variable' => $id, 'style' => 'register-field-error' ));
        $output[] = $this->getText($msg, array( 'style' => 'register-field-error-text' ));
        return $output;
    }

    public function getFieldWithValidation($id, $fieldname, $error=false, $type='text', $submit_menu_id=false){
        
        $class = ( $error ? 'register-field-error' : 'register-field' );

        $this->data->scroll[] = $this->getFieldtext($this->getVariable($id), array( 'variable' => $id, 'hint' => $fieldname, 'style' => $class ));

        if ( $error ) {
            $this->data->scroll[] = $this->getText($error, array( 'style' => 'register-field-error-text' ));
        }
    }

    public function beforeFinishRegistration(){
        $this->saveVariable('reg_phase','complete');
        // $this->closeLogin();

        if($this->getSavedVariable('fb_universal_login')){
            $this->saveVariable('fb_id',$this->getSavedVariable('fb_universal_login'));
            $this->deleteVariable('fb_universal_login');
            $this->saveVariable('logged_in',1);
        }

        if(!$this->getSavedVariable('real_name') AND $this->getSavedVariable('name')){
            $this->saveVariable('real_name',$this->getSavedVariable('name'));
        }

    }

    public function geolocateMe() {
        $this->data->footer[] = $this->getText('There was an error locating you. If you disabled the location permission, go to settings and enable location for this app. ',array('style' => 'register-text-step-2'));
        $btnaction1 = new StdClass();
        $btnaction1->action = 'submit-form-content';
        $btnaction1->id = 'save-variables';

        $btnaction2 = new StdClass();
        $btnaction2->action = 'ask-location';
        $btnaction2->sync_upload = false;

        $buttonparams['onclick'] = array($btnaction1,$btnaction2);
        $buttonparams['style'] = 'general_button_style_footer';
        $buttonparams['id'] = 'dolocate';
        $this->data->footer[] = $this->getText('Locate me',$buttonparams);
    }

    public function setBackButton(){
        if($this->fblogin === false AND !$this->getSavedVariable('instagram_token')) {
            if ($this->getConfigParam('login_branch')) {
                $br = $this->getConfigParam('login_branch');

                $this->loadBranchList();

                if(isset($this->available_branches[$br])){
                    $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                        'style' => 'register-text-step-2',
                        'id' => 'back',
                        'action' => 'open-branch',
                        'config' => $this->getConfigParam('login_branch'),
                    ));
                }
            }
        }
    }

    public function collectName(){
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

    public function collectLocation() {
        $loc = $this->getCollectLocation();

        if ($loc){
            $this->data->onload[] = $loc;
        }
    }

    public function checkName( $name ) {

        if ( empty($name) ) {
            return false;
        }

        if ( $this->getConfigParam( 'validate_first_name_only' ) AND $name ) {
            return true;
        } else {
            $name = explode(' ', $name);

            if (isset($name[0]) AND strlen($name[0]) > 1 AND isset($name[1]) AND strlen($name[1]) > 1) {
                return true;
            } else {
                return false;
            }
        }
        
    }

    private function collectNameSub()
    {
        if ($this->getConfigParam('collect_name',1)) {
            $realname = $this->getVariable('real_name');
            $name = $this->getVariable('name');

            if ($realname AND !$name) {
                $this->saveVariable('name',$realname);
            }

            $error = $this->checkForError('real_name','{#please_input_first_and_last#}');
            $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png',$this->getVariableId('real_name'),'{#name#}',$error);
        }
    }

}