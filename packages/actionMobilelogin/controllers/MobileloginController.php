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
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class MobileloginController extends ArticleController {

    public $data;
    public $configobj;
    public $theme;
    public $fields = array('password1' => 'password1', 'password2' => 'password2');

    public function tab1(){

        $this->data = new StdClass();
        $this->initLoginModel();

        if($this->getConfigParam('collect_push_permission')){
            $pusher = $this->collectPushPermission();
            if($pusher){
                $this->data->onload[] = $pusher;
            }
        }

        /* touch flow:
        - when finishing login, we check if touch id is supported
        - if it supported we request touch id in finishLogin and set session var to handle this case
        - depending on the result, we might set the touchid
        */

        if($this->checkTouch() AND !$this->menuid){
            return $this->data;
        }

        /* role extension */
        if($this->menuid == 'role_influencer'){
            $this->saveVariable('role','influencer');
        }

        if($this->menuid == 'role_brand'){
            $this->saveVariable('role','brand');
        }

        if($this->getConfigParam('ask_for_role')){
            if(!$this->getSavedVariable('role')){
                $this->setRole();
                return $this->data;
            }
        }

        if($this->getSavedVariable('reset_in_progress')){
            $this->passResetCode();
            return $this->data;
        }

        //$this->data->scroll[] = $this->getText('login-var'.$this->getSavedVariable('logged_in'));

        /* so if the login is in "reset mode" you can only enter the code or resend */
        if($this->getSavedVariable('pass_reset') AND $this->menuid != 'reset-password' AND $this->menuid != 'cancel-pass-reset'){
            $this->passResetCode();
            return $this->data;
        }

        /* universal login is set by the api and variable includes the fbid */
        if($this->getSavedVariable('fb_universal_login') > 10 AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalFacebookLogin();
            return $this->data;
        }

        if($this->getSavedVariable('instagram_temp_token') AND strlen($this->getSavedVariable('instagram_temp_token')) > 10 AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalInstagramLogin();
            return $this->data;
        }

        if($this->getSavedVariable('instagram_token') AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalInstagramLogin();
            return $this->data;
        }

        if($this->getSavedVariable('twitter_token') AND strlen($this->getSavedVariable('twitter_token_secret')) > 10 AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalTwitterLogin();
            return $this->data;
        }

        if($this->getSavedVariable('google_token') AND strlen($this->getSavedVariable('google_token')) > 10 AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalGoogleLogin();
            return $this->data;
        }

        switch($this->menuid){
            case 'logout':
                $this->doLogout();
                break;

            case 'reset-password':
                $this->doPassReset();
                break;

            case 'pass-code':
                $this->passResetCode();
                break;

            case 'cancel-pass-reset':
                $this->cancelPassReset();
                break;

            case 'do-fb-login':
                $this->doFbLogin();
                break;

            case 'do-fb-login-alreadylogged':
                $this->doFbLogin(false);
                break;

            case 'do-regular-login':
                $this->doLogin();
                break;

            case 'do-remote-login':
                $this->doRemoteLogin();
                break;

            case 'reset-password-form':
                $this->lostPassForm();
                break;

            case 'login-form':
                $this->loginForm();
                break;

            case 'show-loader';
                //$this->showLoader();
                break;

            default:
                $this->loginForm();
                break;
        }

        return $this->data;
    }

    public function tab2(){
        $this->data = new StdClass();
        $this->lostPassForm();
        return $this->data;
    }

    public function checkTouch(){

        /* case 1: touchid is found associated with the user */
        if($this->getSavedVariable('touchid_supported') AND $this->getSavedVariable('deviceid')){
            if(!isset($_REQUEST['touchid_result']) AND $this->getSavedVariable('logged_in') != '1') {
                if ($this->loginmodel->checkTouch($this->getSavedVariable('deviceid'))) {
                    $this->sessionSet('touchlogin', 1);
                    $complete = new StdClass();
                    $complete->action = 'request-touchid';
                    $complete->action_config = '{#login_with_touch_id#}';

                    $this->data->footer[] = $this->getRow(array(
                        $this->getText('{#login_with_touch_id#}', array(
                            'id' => 'id',
                            'onclick' => $complete,
                            'margin' => '15 0 0 0',
                            "width" => "75%",
                            "text-align" => 'center',
                            "color"=> $this->colors['top_bar_text_color'],
                            "background-color"=> $this->color_topbar,
                            "font_weight"=> "bold",
                            "font_ios"=>"Frutiger65",
                            "font_android"=>"Frutiger65",
                            "height"=> "50",
                            "use_clipping"=> "0"
                        ))
                    ), array(
                        'text-align' => 'center',
                        'margin' => '10 0 0 0'
                    ));
                    
                    if ( empty($this->menuid) ) {
                        $this->loginForm();
                    }

                    return true;
                }
            }
        }

        /* case 2: touchid login in progress. Either completes the login or fails */
        if($this->sessionGet('touchlogin')){
            if($_REQUEST['touchid_result'] == 1){
                $this->sessionSet('touch', '0');
                $this->sessionSet('touchlogin', '0');

                if($this->handleTouchLogin()){
                    return true;
                }
            }
        }

        return false;
    }

    public function doRemoteLogin(){
        return false;
    }

    public function cancelPassReset(){
        $this->deleteVariable('pass_reset');
        $this->loginForm();
    }


    public function setRole($error=false){

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('login-logo.png') ) {
            $image_file = 'login-logo.png';
        }

        if(isset($image_file)){
            $this->data->scroll[] = $this->getImage( $image_file );
        }

        if($error){
            $this->data->footer[] = $this->getText($error,array('style' => 'register-text-step-error'));
        }

        $this->data->footer[] = $this->getTextbutton(strtoupper('{#influencer#}'),array('style' => 'didot_blue_button','id' => 'role_influencer'));
        $this->data->footer[] = $this->getTextbutton(strtoupper('{#brand#}'),array('style' => 'didot_hollow_button_invert','id' => 'role_brand'));
        $this->data->footer[] = $this->getSpacer('20');


    }


    public function showLoader(){
        $this->data->scroll[] = $this->getSpacer('40');
        $this->data->scroll[] = $this->getLoader('',array('color' => '#000000'));
        $this->data->scroll[] = $this->getText('{#loading#}',array('style' => 'mobile-login-general-text'));
        $this->data->footer[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel'));
        Appcaching::setGlobalCache($this->playid.'-regstart',1);
    }

    public function handleUniversalFacebookLogin(){

        /* we are not using fb_id variable yet at this point */
        $this->loginmodel->thirdpartyid = $this->getSavedVariable('fb_universal_login');
        $is_user = $this->loginmodel->loginWithThirdParty('facebook');

        if($is_user){
            $this->setHeader();

            $this->playid = $is_user;
            $this->finishLogin(true,true,__LINE__);
        } else {
            $this->setHeader();
            $this->loginmodel->addFbInfoToUser();
            $text = '{#facebook_connected_create_profile#}';
            $this->data->scroll[] = $this->getText($text,array( 'style' => 'mobile-login-general-text'));

            $complete = new StdClass();
            $complete->action = 'open-branch';
            $complete->action_config = $this->getConfigParam('register_branch');
            $this->data->onload[] = $complete;

            // $this->data->scroll[] = $this->getTextbutton('{#profile_registration#}', array(
            //         'style' => 'general_button_style',
            //         'id' => 'do-regular-registration',
            //         'action' => 'open-branch',
            //         'config' => $this->getConfigParam('register_branch')
            //     )
            // );

            return true;
        }
    }

    public function handleUniversalInstagramLogin(){
        $this->setHeader();

        $apikey = Aemobile::getConfigParam($this->gid,'instagram_apikey');
        $apisecret = Aemobile::getConfigParam($this->gid,'instagram_secretkey');

        /* the only thing we have initially is the token */
        if($this->getSavedVariable('instagram_temp_token') OR $this->getSavedVariable('instagram_token')){
            $token = $this->getSavedVariable('instagram_temp_token') ? $this->getSavedVariable('instagram_temp_token') : $this->getSavedVariable('instagram_token');
            $insta = new InstagramConnector($apikey,$apisecret,$token);
            $self = $insta->get('users/self');

            //print_r($self);die();

            if($self->data->username){
                $this->loginmodel->thirdpartyid = $self->data->username;
                $is_user = $this->loginmodel->loginWithThirdParty('instagram');

                if($is_user){
                    $this->playid = $is_user;
                    $this->finishLogin(true,false,__LINE__);
                } else {
                    if($this->getSavedVariable('instagram_temp_token')){
                        if(isset($self->data)){
                            $this->loginmodel->addInstagramInfoToUser($self,$token);
                            $text = '{#instagram_connected_create_profile#}';
                            $this->data->scroll[] = $this->getText($text,array( 'style' => 'mobile-login-general-text'));

                            $complete = new StdClass();
                            $complete->action = 'complete-action';
                            $this->data->onload[] = $complete;

                            $complete = new StdClass();
                            $complete->action = 'open-branch';
                            $complete->action_config = $this->getConfigParam('register_branch');
                            $this->data->onload[] = $complete;

                        } else {
                            $this->loginForm('Login error :(');
                        }
                    } elseif($this->getSavedVariable('reg_phase') != 'complete'){
                        $complete = new StdClass();
                        $complete->action = 'open-branch';
                        $complete->action_config = $this->getConfigParam('register_branch');
                        $this->data->onload[] = $complete;
                    }

                    return true;
                }

            } else {
                $this->loginForm('{#instagram_login_failed#}');
            }

        } else {
            $this->data->scroll[] = $this->getText('{#instagram_login_failed#}',array( 'style' => 'mobile-login-general-text'));
        }




    }


    public function handleUniversalTwitterLogin(){

        /* we are not using fb_id variable yet at this point */
        $this->loginmodel->thirdpartyid = $this->getSavedVariable('twitter_id');
        $is_user = $this->loginmodel->loginWithThirdParty('twitter');
        $this->setHeader();

        if($is_user){
            $this->playid = $is_user;
            $this->finishLogin(true,false,__LINE__);
        } else {
            if($this->getSavedVariable('twitter_token') AND $this->getSavedVariable('twitter_token_secret')){
                $text = '{#twitter_connected_create_profile#}';
                $this->data->scroll[] = $this->getText($text,array( 'style' => 'mobile-login-general-text'));

                $complete = new StdClass();
                $complete->action = 'open-branch';
                $complete->action_config = $this->getConfigParam('register_branch');
                $this->data->onload[] = $complete;

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
            }

            return true;
        }
    }


    public function handleUniversalGoogleLogin(){

        /* we are not using fb_id variable yet at this point */
        $this->loginmodel->thirdpartyid = $this->getSavedVariable('google_email');
        $is_user = $this->loginmodel->loginWithThirdParty('google');
        $this->setHeader();

        if($is_user){
            $this->playid = $is_user;
            $this->finishLogin(true,false,__LINE__);
        } else {
            if($this->getSavedVariable('google_token')){
                $text = '{#google_connected_create_profile#}';
                $this->data->scroll[] = $this->getText($text,array( 'style' => 'mobile-login-general-text'));

                $vars['email'] = $this->getSavedVariable('google_email');
                $vars['firstname'] = $this->getSavedVariable('google_first_name');
                $vars['lastname'] = $this->getSavedVariable('google_last_name');
                $vars['real_name'] = $vars['firstname'] .' ' .$vars['lastname'];
                $vars['profilepic'] = $this->getSavedVariable('google_profilepic');

                AeplayVariable::saveNamedVariablesArray($vars,$this->playid,$this->gid);

                $complete = new StdClass();
                $complete->action = 'open-branch';
                $complete->action_config = $this->getConfigParam('register_branch');
                $this->data->onload[] = $complete;

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
            }

            return true;
        }
    }

    public function handleTouchLogin(){

        /* we are not using fb_id variable yet at this point */
        $this->loginmodel->thirdpartyid = $this->getSavedVariable('touchid');
        $is_user = $this->loginmodel->loginWithThirdParty('touchid');

        if($is_user AND $_REQUEST['touchid_result'] == 1){
            $this->playid = $is_user;
            $this->finishLogin(true,false,__LINE__,true);
        } else {
            $this->loginForm('{#no_touch_id_provided#}. 
{#you_can_login_with_credentials#}');
        }

        return true;
    }

    public function doFbLogin($firsttime=true){
        $fbtoken = $this->getSavedVariable('fb_token');

        if($fbtoken){
            $fbinfo = ThirdpartyServices::getUserFbInfo($fbtoken);

            if ( !isset($fbinfo->id) ) {
                $this->loginForm('Login error :(');
                return false;
            }

            if ( $firsttime == false) {
                $this->finishLogin(true, true, __LINE__, true);

            } elseif( $firsttime == true ) {
                
                if($this->getSavedVariable('password') AND strlen($this->getSavedVariable('password')) > 2){
                    $skipreg = true;
                } else {
                    $skipreg = false;
                }

                $this->finishLogin($skipreg,true,__LINE__);
            }

        } else {
            /* this is a case where user is logged in with facebook, but its actually a new user */
            $this->handleUniversalFacebookLogin();
        }

    }

    public function setHeader($padding=false){
        if($padding){
            $height = $padding;
        }elseif($this->aspect_ratio > 0.57) {
            $height = 20;
        } else {
            $height = 45;
        }

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('login-logo.png') ) {
            $image_file = 'login-logo.png';
        }

        $this->data->scroll[] = $this->getSpacer($height);

        if(isset($image_file)){
            $this->data->scroll[] = $this->getImage( $image_file );
        }
        
        $this->data->scroll[] = $this->getSpacer('20');
    }

    public function doLogin(){

        $id_email = $this->getVariableId('email');
        $id_password = $this->getVariableId('password');

        $email = strtolower($this->getSubmitVariable($id_email));
        $password = sha1(strtolower(trim($this->getSubmitVariable($id_password))));

        if(strlen($this->getSubmitVariable($id_password)) > 3) {
            $this->loginmodel->password = $password;
            $this->loginmodel->username = $email;

            $login = $this->loginmodel->doLogin();
            //$this->data->scroll[] = $this->getText($password);

            if($login){
                $this->playid = $login;
                $this->loadVariables();
                $this->finishLogin(true,true,__LINE__);
            } else {
                $this->loginForm('{#user_not_found_or_password_wrong#}');
            }
        } else {
            $this->loginForm('{#user_not_found_or_password_wrong#}');
        }
    }

    public function loggingInHeader(){
        $this->data->scroll[] = $this->getFullPageLoader();
        $this->data->scroll[] = $this->getText('{#logging_in#}',array( 'style' => 'mobile-login-general-text'));
    }

    /* this sets all the variables & resets branches */
    public function finishLogin($skipreg=false,$fblogin=false,$line=false,$tokenlogin=false){
        $this->loggingInHeader();
        $this->addToDebug('Reg line:' .$line);

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;

        if($skipreg){
            $this->addToDebug('closing shop:' .$line);
            AeplayBranch::closeBranch($this->getConfigParam('register_branch'),$this->playid);
            AeplayBranch::closeBranch($this->getConfigParam('login_branch'),$this->playid);
            AeplayBranch::activateBranch($this->getConfigParam('logout_branch'),$this->playid);
            $this->saveVariable('logged_in',1);
            if($fblogin OR $tokenlogin){
                $this->deleteVariable('fb_universal_login');
            }
        }

        if($this->getSavedVariable('oauth_in_progress')){
            $string = explode('_',$this->getSavedVariable('oauth_in_progress'));
            if(isset($string[1])){
                $complete = new StdClass();
                $complete->action = 'open-action';
                $complete->action_config = $string[1];
                $this->data->onload[] = $complete;
            }
        }



    }

    public function doPassReset(){
        $user_mail = $this->getSubmitVariable('email-to-reset') ;

        if ( !$user_mail OR $user_mail == '0') {
            $this->lostPassForm('{#check_email#}');
            return true;
        }

        $validator = new CEmailValidator;
        $validator->checkMX = true;

        if(!$validator->validateValue($user_mail)){
            $this->lostPassForm('{#not_a_valid_email#}');
            return true;
        }

        $this->saveVariable('email', $user_mail);

        $obj = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->getVariableId('email'),'value' => $user_mail));

        if(!is_object($obj)){
            $this->lostPassForm('{#email_not_found#}');
            return true;
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($obj->play_id);
        
        if(!isset($vars['password']) OR !$vars['password']){
            $this->lostPassForm('{#logged_in_with_another_service_cant_reset_password#}');
            return true;
        } elseif(isset($vars['fb_id']) AND $vars['fb_id']){
            if(!isset($vars['password']) OR !$vars['password']){
                $this->lostPassForm('{#logged_in_with_another_service_cant_reset_password#}');
                return true;
            }
        }

        /* very specific case where user has logged in with one account & then does a reset for another account */
        $code = Helper::generateShortcode();

/*        if($obj->play_id != $this->playid){
            $extravars['reset_in_progress'] = 1;
            $extravars['logged_in'] = 0;
            $extravars['pass_reset'] = $code;
            $this->loginmodel->switchPlay($obj->play_id,false,$extravars);
            $this->playid = $this->loginmodel->playid;
            $this->loadVariables();
            $complete = true;
        } else {*/
            $this->saveVariable('pass_reset',$code);
        //}

        $from = isset($this->appinfo->name) ? $this->appinfo->name : 'Appzio';
        $mail = new YiiMailMessage;
        
        $body = $this->localizationComponent->smartLocalize('{#we_received_a_request_to_change_the_password#}');
        $body .= "<br /><br />";
        $body .= $this->localizationComponent->smartLocalize('{#if_you_want_to_change_your_password_please_enter_the_following_code_to_reset_the_application_on_your_mobile#}: ');
        $body .= "<br /><br />";
        $body .= $this->localizationComponent->smartLocalize('{#reset_password#}: ') . $code;
        $body .= "<br /><br />";

        $body .= "Kind regards, <br>";
        $body .= "Your DGF Continuous Improvement/First Choice Team . <br>";
        $body .= "Dgf-firstchoice@dhl.com";

        $mail->setBody($body, 'text/html');
        $mail->addTo( $user_mail );
        $mail->from = array('info@appzio.com' => $from);
        $mail->subject = $this->localizationComponent->smartLocalize('{#password_reset#}');

        try {
            Yii::app()->mail->send($mail);
        } catch (Exception $e) {
            $this->setError($e);
        }

        $this->passResetCode();


    }

    public function passResetCode(){
        if($this->menuid == 'pw-reset' OR $this->menuid == 'validate-new-pass'){
            $this->validateCode();
        } else {
            $this->getCodeEntryForm();
        }
    }

    public function validateCode(){
        $submit = $this->getSubmitVariable($this->getVariableId('password_code'));

        if ( $submit == $this->getSavedVariable('pass_reset') OR $this->menuid == 'validate-new-pass' ) {

            if($this->menuid == 'validate-new-pass'){
                $error = $this->checkForError('password_validity','4 {#chars_at_least#}');
                $error2 = $this->checkForError('password_match','Passwords don\'t match');

                if($error == false AND $error2 == false){
                    $pass = $this->getSubmitVariable($this->fields['password1']);

                    if($pass){
                        /* save password */
                        $email = $this->getSavedVariable('email');
                        $play = $this->loginmodel->findUserWithoutPassword($email);
                        $this->saveRemoteVariable('password', sha1(strtolower(trim($pass))), $play);
                        $this->saveVariable('reset_in_progress', 0);
                        $this->saveVariable('pass_reset', 0);
                        $this->loginForm('{#password_updated_please_login#}');
                        return true;
                    }
                }
            } else {
                $error = false;
                $error2 = false;
            }

            $this->setHeader();
            $this->data->scroll[] = $this->getText('{#choose_new_password#}',array( 'style' => 'mobile-login-general-text'));
            $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->fields['password1'],'{#password#} (4 {#chars_at_least#})',$error,'password');
            $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->fields['password2'],'{#repeat_password#}',$error2,'password','mobilereg_do_registration');
            $this->data->scroll[] = $this->getSpacer('5');
            $this->data->scroll[] = $this->getTextbutton('{#save_new_password#}',array('style' => 'general_button_style','id' => 'validate-new-pass'));
            return true;
        } else {
            $this->getCodeEntryForm();
            $this->data->scroll[] = $this->getText('{#wrong_code#}',array( 'style' => 'mobile-login-general-text'));
            return true;
        }

    }




    public function checkForError($field,$msg){

        switch($field){
            case 'password_match':
                $value1 = $this->getVariable($this->fields['password1']);
                $value2 = $this->getVariable($this->fields['password2']);
                $result = ( $value1 == $value2 ? true : false );
                break;

            case 'password_validity':
                $value1 = $this->getVariable($this->fields['password1']);
                $result = ( strlen($value1) > 3 ? true : false );
                break;
        }

        if ( empty($result) ) {
            return $msg;
        }

        return false;
    }


    public function saveLogoutData(){
        $this->saveVariable('logged_in','0');
        $this->saveVariable('fb_universal_login','0');
        $this->deleteVariable('instagram_token');
        $this->deleteVariable('instagram_temp_token');
        $this->deleteVariable('twitter_token');
        $this->deleteVariable('google_token');
        $this->deleteVariable('twitter_token_secret');
        $this->deleteVariable('oauth_raw_info');
        $this->deleteVariable('access_token');
        $this->deleteVariable('refresh_token');
    }


    public function logoutHeader(){
        $this->data->scroll[] = $this->getSpacer(120);
        $this->data->scroll[] = $this->getFullPageLoader('#000000','{#logging_out#}');
        //$this->data->scroll[] = $this->getText('{#logging_out#}',array( 'style' => 'mobile-login-general-text'));
    }

    public function doLogout(){
        $this->logoutHeader();
        $this->saveLogoutData();

        AeplayBranch::activateBranch($this->getConfigParam('login_branch'),$this->playid);
        AeplayBranch::activateBranch($this->getConfigParam('register_branch'),$this->playid);

        $complete = new StdClass();
        $complete->action = 'fb-logout';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;

        if($this->getConfigParam('instagram_login_provider')){
            $complete = new StdClass();
            $complete->action = 'open-action';
            $complete->action_config = $this->getConfigParam('instagram_login_provider');
            //$complete->open_popup = 1;
            $this->data->onload[] = $complete;
            //$this->getConfigParam('instagram_login_provider');
        }


    }


    public function loginForm($error=false){
        $loggedin = $this->getSavedVariable('logged_in');

        if($loggedin == 1){
            $this->logoutForm();
            return true;
        }

        if($this->getConfigParam('only_logout')){
            $this->data->scroll[] = $this->getFullPageLoader();
            return true;
        }

        $this->setHeader();
        $reg = Appcaching::getGlobalCache( $this->playid .'-' .'registration');

        if($reg){
            Yii::app()->cache->set( $this->playid .'-' .'registration',false );
        }

        $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png',$this->getVariableId('email'),'{#email#}',$error,'email');
        $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->getVariableId('password'),'{#password#}',false,'password','do-regular-login');
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->scroll[] = $this->getTextbutton('{#login#}',array('style' => 'general_button_style','id' => 'do-regular-login'));

        if( $this->getConfigParam('facebook_enabled')){
            if($this->fblogin === true AND !$loggedin AND $this->getSavedVariable('fb_token')){
                $this->data->scroll[] = $this->getFacebookSignInButton('do-fb-login-alreadylogged',true);
            } else {
                $this->data->scroll[] = $this->getFacebookSignInButton('do-fb-login');
            }

            $this->data->scroll[] = $this->getSpacer('5');
        }

        if( $this->getConfigParam('instagram_enabled') AND $this->getConfigParam('instagram_login_provider')) {
            $this->data->scroll[] = $this->getInstagramSignInButton($this->getConfigParam('instagram_login_provider'));
        }

        if($this->getSavedVariable('instagram_error')){
            $this->data->scroll[] = $this->getText($this->getSavedVariable('instagram_error'));
        }

        if( $this->getConfigParam('twitter_enabled')) {
            $this->data->scroll[] = $this->getTwitterSignInButton();
        }

        $content4[] = $this->getTextbutton('{#forgot_password#}',array('id' => 'reset-password-form',
            'background-color' => $this->colors['top_bar_color'],
            'color' => $this->colors['top_bar_text_color'],
            'border-radius' => '4',
            'font-size' => '11',
            'height' => '45',
            'width' => '48%'
        ));
        $content4[] = $this->getVerticalSpacer('4%');

        $content4[] = $this->getTextbutton('{#signup_with_email#}', array('action' => 'open-branch','id' => '393933','config' => $this->requireConfigParam('register_branch'),
            'background-color' => $this->colors['top_bar_color'],
            'color' => $this->colors['top_bar_text_color'],
            'border-radius' => '4',
            'font-size' => '11',
            'height' => '45',
            'width' => '48%'
            ));

        $this->data->scroll[] = $this->getRow($content4,array('margin' => '10 40 0 40','text-align' =>'center'));

        if($this->getConfigParam('ask_for_role')){
            if($this->getSavedVariable('role') == 'influencer'){
                $col[] = $this->getTextbutton(strtoupper('{#influencers#}'),array('style' => 'didot_blue_button_small','id' => 'role_influencer'));
                $col[] = $this->getVerticalSpacer('4%');
                $col[] = $this->getTextbutton(strtoupper('{#brands#}'),array('style' => 'didot_hollow_button_invert_small','id' => 'role_brand'));
            } else {
                $col[] = $this->getTextbutton(strtoupper('{#influencers#}'),array('style' => 'didot_hollow_button_invert_small','id' => 'role_influencer'));
                $col[] = $this->getVerticalSpacer('4%');
                $col[] = $this->getTextbutton(strtoupper('{#brands#}'),array('style' => 'didot_blue_button_small','id' => 'role_brand'));
            }

            $this->data->footer[] = $this->getRow($col,array('text-align' => 'center','padding' => '15 15 15 15'));
        }
    }

    public function logoutForm(){
        $this->setHeader();

        $string = $this->localizationComponent->smartLocalize('{#logout#}');
        $onclick = new stdClass();
        $onclick->action = 'logout';

        $clicker[] = $onclick;
        $clicker[] = $this->getOnclick('id',false,'logout');

        $this->data->scroll[] = $this->getTextbutton(strtoupper($string), array(
        	'style' => 'button_imate_red',
	        'onclick' => $clicker,
	        'id' => 'logout'
        ));

//      $this->loadVariableContent(true);
        $this->data->scroll[] = $this->getText('{#click_on_the_button_below_to_logout#}',array( 'style' => 'mobile-login-general-text'));
        $this->data->scroll[] = $this->getSpacer(50);
    }

    public function getCodeEntryForm(){
        $this->setHeader();
        $this->data->scroll[] = $this->getText('{#password_code_hint#}',array( 'style' => 'mobile-login-general-text'));
        $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->getVariableId('password_code'),
            '{#enter_code_from_email#}',false,'text',false,'lowercase');

        $params['margin'] = '10 42 0 42';
        $params['align'] = 'center';
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->scroll[] = $this->getTextbutton('{#enter_code#}',array('style' => 'general_button_style','id' => 'pw-reset'));
        $this->data->scroll[] = $this->getTextbutton('{#resend_password_reset#}',array('style' => 'general_button_style','id' => 'reset-password'));
        $this->data->scroll[] = $this->getTextbutton('{#cancel#}',array('style' => 'button_imate_red','id' => 'cancel-pass-reset'));
    }

    public function lostPassForm($error=false){

        $this->setHeader();
        $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png','email-to-reset','Email',$error,'text','reset-password','email');

        $params['margin'] = '10 42 0 42';
        $params['align'] = 'center';
        $this->data->scroll[] = $this->getSpacer('5');

        $this->data->scroll[] = $this->getTextbutton('{#reset_password#}',array('style' => 'general_button_style','id' => 'reset-password'));
        $this->data->scroll[] = $this->getTextbutton('{#cancel#}',array('style' => 'button_imate_red','id' => 'login-form'));

    }

}