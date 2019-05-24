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
Yii::import('application.modules.aelogic.packages.actionMobilelogin.controllers.*');

/* NOTE!
    there is a lot of copy-paste code from MobileloginController.php which is not
    very cool. Some of the things shared here should be abstracted, so simply cleaned up.
*/


class MobileoauthController extends MobileloginController {

    public $data;
    public $configobj;
    public $theme;
    public $fields = array('password1' => 'password1', 'password2' => 'password2');

    public $strings = array('logging_in' => '{#logging_in#}');

    /** @var MobileloginModel */
    public $loginmodel;

    /* this has the token from the other app */
    public $foreign_token;
    
    public function tab1(){

        $this->data = new StdClass();
        $this->initModel();
        
        if($this->getConfigParam('mode') == 'connector') {
            $this->handleOauthConnection();
            return $this->data;
        }

        if($this->getConfigParam('collect_push_permission')){
            $pusher = $this->collectPushPermission();
            if($pusher){
                $this->data->onload[] = $pusher;
            }
        }

        if(strstr($this->menuid,'doconnect_')){
            $parts = explode('_',$this->menuid);
            //$this->data->scroll[] = $this->getText('hey there',array('style' => 'register-text-step-2'));

            /* for added security we see that the checksum matches */
            if(isset($parts[0]) AND isset($parts[1]) AND isset($parts[2]) AND $parts[1] == md5($this->playid .$this->gid)){
                $this->foreign_token = $parts[2];
                $this->doOauth();
            } else {
                if($parts[1] != md5($this->playid .$this->gid)){
                    $this->data->scroll[] = $this->getText('prob with return token',array('style' => 'register-text-step-2'));
                }

                $this->data->scroll[] = $this->getText('{#authentication_error#}',array('style' => 'register-text-step-2'));

                $this->data->scroll[] = $this->getText('{#try_login_again#}',array(
                    'style' => 'golf-big-green-button',
                    'onclick' => $this->getOauthSignIn()
                ));

            }

            return $this->data;
        }

        /* universal login is set by the api and variable includes the fbid */
        if($this->getSavedVariable('fb_universal_login') > 10 AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalFacebookLogin();
            return $this->data;
        }

        if($this->getSavedVariable('instagram_token') AND strlen($this->getSavedVariable('instagram_token')) > 10 AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalInstagramLogin();
            return $this->data;
        }

        if($this->getSavedVariable('twitter_token') AND strlen($this->getSavedVariable('twitter_token_secret')) > 10 AND $this->getSavedVariable('logged_in') != '1'){
            $this->handleUniversalTwitterLogin();
            return $this->data;
        }

        switch($this->menuid){

            case 'oauth':
                $this->doOauth();
                break;

            case 'logout':
                $this->doLogout();
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

            case 'login-form':
                $this->loginForm();
                break;

            case 'show-loader';
                $this->showLoader();
                break;

            default:
                $this->loginForm();
                break;
        }

        return $this->data;
    }


    public function doOauth(){
        $obj = new MobileoauthModel();
        $obj->varcontent = $this->varcontent;
        $obj->configobj = $this->configobj;
        $obj->gid = $this->gid;
        $obj->playid = $this->playid;
        $login = $obj->doAppzioUpdate($this->foreign_token);

       if($login == true){
            $this->finishLogin(true);
        } else {
            $this->data->scroll[] = $this->getText('{#failed#}',array('style' => 'register-text-step-2'));
        }
    }

    public function initModel(){
        $this->loginmodel = new MobileloginModel();
        $this->loginmodel->userid = $this->userid;
        $this->loginmodel->playid = $this->playid;
        $this->loginmodel->gid = $this->gid;
        $this->loginmodel->password = $this->getSavedVariable('password');
        $this->loginmodel->fbid = $this->getSavedVariable('fbid');
        $this->loginmodel->fbtoken = $this->getSavedVariable('fb_token');
        $this->loginmodel->password = $this->getSavedVariable('password');
    }

    public function handleOauthConnection(){

        $this->data->scroll[] = $this->getFullPageLoader();

        if($this->getSavedVariable('logged_in') != 1){
            $onclick2 = new StdClass();
            $onclick2->id = 'link';
            $onclick2->action = 'open-branch';
            $onclick2->sync_open = 1;
            $onclick2->action_config = $this->getConfigParam('login_branch');
            $string = $this->menuid .'_' .$this->action_id;
            $this->saveVariable('oauth_in_progress',$string);

            $this->data->scroll[] = $this->getSpacer(50);
            $this->data->scroll[] = $this->getText('{#please_login_first_oauth#}',array('text-align' => 'center','font-size' => '13'));
            $this->data->scroll[] = $this->getSpacer(20);
            $this->data->scroll[] = $this->getText('{#login#}',array('onclick' => $onclick2,'style' => 'general_button_style'));

            //$this->data->onload[] = $onclick2;

            $onclick2 = new StdClass();
            $onclick2->action = 'list-branches';
            $this->data->onload[] = $onclick2;

        } elseif($this->menuid) {
            $this->oauthReturn($this->menuid);
        } elseif($this->getSavedVariable('oauth_in_progress')){
            $parts = explode('_',$this->getSavedVariable('oauth_in_progress'));
            if(isset($parts[0])){
                $this->oauthReturn($parts[0]);
            } else {
                $this->data->scroll[] = $this->getText('unknown error');
            }
        }
    }

    public function oauthReturn($foreign_token){
        $token = Aeaccesstokens::addToken($this->userid,$this->appinfo->api_key,$this->playid,'86400',false);
        $return = 'doconnect_' .$foreign_token .'_' .$token;
        $url = $this->getConfigParam('app_link') .'://open?action_id=' .$this->getConfigParam('action_id') .'&menuid=' .$return;

        $onclick1 = new StdClass();
        $onclick1->id = 'link';
        $onclick1->action = 'open-branch';
        $onclick1->sync_open = 1;
        $onclick1->action_config = $this->getConfigParam('default_branch');

        $this->data->onload[] = $onclick1;

        $onclick2 = new StdClass();
        $onclick2->id = 'link';
        $onclick2->action = 'open-url';
        $onclick2->sync_open = 1;
        $onclick2->action_config = $url;

        $this->data->onload[] = $onclick2;
        $this->deleteVariable('oauth_in_progress');

    }

    public function showLoader(){
        $this->data->scroll[] = $this->getSpacer('40');
        $this->data->scroll[] = $this->getLoader('',array('color' => '#000000'));
        $this->data->scroll[] = $this->getText('{#loading#}',array('style' => 'register-text-step-2'));
        $this->data->footer[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel'));
        Appcaching::setGlobalCache($this->playid.'-regstart',1);
    }

    public function handleUniversalFacebookLogin(){

        /* we are not using fb_id variable yet at this point */
        $this->loginmodel->thirdpartyid = $this->getSavedVariable('fb_universal_login');
        $is_user = $this->loginmodel->loginWithThirdParty('facebook');
        $this->setHeader();

        if($is_user){
            $this->playid = $is_user;
            $this->finishLogin(true,true,__LINE__);
        } else {
            $this->loginmodel->addFbInfoToUser();
            $text = '{#facebook_connected_create_profile#}';
            $this->data->scroll[] = $this->getText($text,array( 'style' => 'register-text-step-2'));

            $complete = new StdClass();
            $complete->action = 'open-branch';
            $complete->action_config = $this->getConfigParam('register_branch');
            $this->data->onload[] = $complete;

            return true;
        }
    }

    public function handleUniversalInstagramLogin(){

        /* we are not using fb_id variable yet at this point */
        $this->loginmodel->thirdpartyid = $this->getSavedVariable('instagram_username');
        $is_user = $this->loginmodel->loginWithThirdParty('instagram');
        $this->setHeader();

        if($is_user){
            $this->playid = $is_user;
            $this->finishLogin(true,false,__LINE__);
        } else {
            if($this->getSavedVariable('instagram_token')){
                $apikey = Aemobile::getConfigParam($this->gid,'instagram_apikey');
                $apisecret = Aemobile::getConfigParam($this->gid,'instagram_secretkey');

                $insta = new InstagramConnector($apikey,$apisecret,$this->getSavedVariable('instagram_token'));
                $self = $insta->get('users/self');

                if(isset($self->data)){
                    $this->loginmodel->addInstagramInfoToUser($self);
                    $text = '{#instagram_connected_create_profile#}';
                    $this->data->scroll[] = $this->getText($text,array( 'style' => 'register-text-step-2'));

                    $complete = new StdClass();
                    $complete->action = 'open-branch';
                    $complete->action_config = $this->getConfigParam('register_branch');
                    $this->data->onload[] = $complete;

                    $complete = new StdClass();
                    $complete->action = 'complete-action';
                    $this->data->onload[] = $complete;
                } else {
                    $this->loginForm('Login error :(');
                }
            }

            return true;
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
                $this->data->scroll[] = $this->getText($text,array( 'style' => 'register-text-step-2'));

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

        $count = 1;
        $images = array();

        while($count < 8){
            if($this->getConfigParam('actionimage'.$count)){
                $images[] = $this->getConfigParam('actionimage'.$count);
            }

            $count++;
        }

        $image_styles['imgwidth'] = '750';
        $image_styles['imgheight'] = '1103';
        $image_styles['imgcrop'] = 'yes';
        $image_styles['width'] = $this->screen_width;
        $image_styles['priority'] = 1;
        $navi_styles['margin'] = '-50 0 0 0';
        $navi_styles['text-align'] = 'center';

        $totalcount = count($images);
        $items = array();

        foreach ($images as $i => $image) {
            $scroll = array();
            $scroll[] = $this->getImage($image, $image_styles);
            $scroll[] = $this->getSwipeNavi($totalcount, ($i+1), $navi_styles);
            $items[] = $this->getColumn($scroll, array());
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getSwipearea($items),
            ), array( 'margin' => '0 0 0 0' )),
        ), array( 'margin' => '0 0 0 0' ));

    }

    public function doLogin(){

        $id_email = $this->getVariableId('email');
        $id_password = $this->getVariableId('password');

        $email = strtolower($this->getSubmitVariable($id_email));
        $password = sha1(strtolower(trim($this->getSubmitVariable($id_password))));

        $saved_email = strtolower($this->getSavedVariable('email'));
        $saved_password = strtolower($this->getSavedVariable('password'));

        if(strlen($email) > 4 AND strlen($this->getSubmitVariable($id_password)) > 3 AND $saved_email == $email AND $saved_password == $password){
            $this->finishLogin(true,true,__LINE__);
        } elseif($email AND $saved_email == $email) {
            $this->loginForm('{#wrong_password#}');
        } elseif(strlen($this->getSubmitVariable($id_password)) > 3) {
            $this->loginmodel->password = $password;
            $this->loginmodel->username = $email;
            $login = $this->loginmodel->doLogin();

            if($login){
                $this->playid = $login;
                $this->finishLogin(true,true,__LINE__);
            } else {
                $this->loginForm('{#user_not_found_or_password_wrong#}');
            }
        } else {
            $this->loginForm('{#user_not_found_or_password_wrong#}');
        }
    }

    /* this sets all the variables & resets branches */
    public function finishLogin($skipreg=false,$fblogin=false,$line=false,$tokenlogin=false){
        $this->data = new stdClass();
        $this->data->scroll[] = $this->getText($this->strings['logging_in'],array( 'style' => 'register-text-step-2'));
        $this->addToDebug('Reg line:' .$line);

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

        $complete = new StdClass();
        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;


    }

    public function doLogout(){
        $this->data->scroll[] = $this->getSpacer(120);
        $this->data->scroll[] = $this->getText('{#logging_out#}',array( 'style' => 'register-text-step-2'));

        $this->saveVariable('logged_in','0');
        $this->saveVariable('fb_universal_login','0');
        $this->deleteVariable('instagram_token');
        $this->deleteVariable('twitter_token');
        $this->deleteVariable('twitter_token_secret');

        AeplayBranch::activateBranch($this->getConfigParam('login_branch'),$this->playid);
        AeplayBranch::activateBranch($this->getConfigParam('register_branch'),$this->playid);

        $complete = new StdClass();
        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'fb-logout';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;
    }


    public function loginForm($error=false){
        $loggedin = $this->getSavedVariable('logged_in');


        $cache = Appcaching::getGlobalCache('schemecheck-'.$this->playid);
        
        /* check if the other app is installed */
        if(!$this->getSavedVariable('oauth_other_app_installed') AND !$cache){
            $checker = new stdClass();
            $checker->action = 'check-scheme';
            $checker->variable = 'oauth_other_app_installed';
            $checker->action_config = $this->getConfigParam('app_link') .'://';
            $this->data->onload[] = $checker;
            Appcaching::setGlobalCache('schemecheck-'.$this->playid,true,120);
        }


        if($loggedin == 1){
            $this->logoutForm();
            return true;
        }

        //$this->setHeader();
        $reg = Appcaching::getGlobalCache( $this->playid .'-' .'registration');

        if($reg){
            Yii::app()->cache->set( $this->playid .'-' .'registration',false );
        }

        $this->data->scroll[] = $this->getImage('golfizz-logo.png',array('margin' => '20 0 10 0'));
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getText('{#invite_friends_to_play#}',array('style' => 'golf-toptext'));
        $this->data->scroll[] = $this->getText('{#keep_your_scorecard#}',array('style' => 'golf-toptext'));
        $this->data->scroll[] = $this->getText('{#greenfee_discounts#}',array('style' => 'golf-toptext'));
        $this->data->scroll[] = $this->getText('{#proshop_discounts#}',array('style' => 'golf-toptext'));
        $this->data->scroll[] = $this->getSpacer('40');

        if($this->getSavedVariable('system_source') == 'client_iphone') {
            $openurl = 'itms://itunes.apple.com/us/app/apple-store/id1150873525?mt=8';
        } else {
            $openurl = 'market://details?id=com.appzio.golfriendapp';
        }

            if($this->getSavedVariable('oauth_other_app_installed')) {
            $this->data->scroll[] = $this->getText('{#use_your_golfriends_account_to_login#}. 
{#if_you_are_not_logged_in_with_golfriends_you_might_have_to_click_login_button_again#}
            ', array('style' => 'golf-toptext',
               // 'onclick' => $this->getOnclick('url', false, 'https://google.com/')
            ));

        } else {
            $this->data->scroll[] = $this->getText('{#we_recommend_you_to_install_golfriends#}',array('style' => 'golf-toptext'));

            $this->data->scroll[] = $this->getText('{#if_you_have_golfriends_installed_you_dont_need_to_register_on_golfizz_separately#}. {#click_here_to_install#}. ', array('style' => 'golf-toptext',
                'onclick' => $this->getOnclick('url', false,$openurl)
            ));

        }
        $this->copyAssetWithoutProcessing('grass-bottom.png');

        if($this->getSavedVariable('oauth_other_app_installed')){
            $col[] = $this->getText('{#login_with_golfriend#}',array(
                'style' => 'golf-big-green-button',
                'onclick' => $this->getOauthSignIn()
            ));
        } else {
            $col[] = $this->getText('{#install_golfriend#}',array(
                'style' => 'golf-big-green-button',
                'onclick' => $this->getOnclick('url',true,$openurl)
            ));
        }

        $checker = new stdClass();
        $checker->action = 'check-scheme';
        $checker->variable = 'oauth_other_app_installed';
        $checker->action_config = $this->getConfigParam('app_link') .'://';

        $this->data->scroll[] = $this->getSpacer('20');
        $this->data->scroll[] = $this->getColumn($col,array(
            //'background-image' => 'grass-bottom.png', 'background-size' => 'cover',
            'vertical-align' => 'middle','text-align' => 'center',
            'width' => $this->screen_width));
        $this->data->scroll[] = $this->getSpacer('20');

        $btns[] = $this->getText('{#login_without_golfriends#}',array('style' => 'golf-bottom-register-btn',
            'onclick' => $this->getOnclick('action',true,$this->getConfigParam('login_action'))));

        $btns[] = $this->getVerticalSpacer('2%',array('background-color' => '#000000'));
        $btns[] = $this->getText('{#register_without_golfriends#}',array('style' => 'golf-bottom-register-btn',
            'onclick' => $this->getOnclick('action',true,$this->getConfigParam('register_action'))));

        if(!$this->getSavedVariable('oauth_other_app_installed')) {

            $this->data->footer[] = $this->getRow($btns);
        }

        $this->data->scroll[] = $this->getText('{#check_if_installed#}', array('style' => 'golf-toptext','onclick' => $checker));

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

        $this->data->scroll[] = $this->getSpacer('20');
    }

    public function getRegisterButton(){
            $onclick2 = new StdClass();
            $onclick2->id = 'link';
            $onclick2->action = 'open-action';
            $onclick2->sync_open = 1;
            $onclick2->action_config = $this->getConfigParam('register');
            return $this->getButtonWithIcon('gf-icon-logo.png', 'insta', '{#register#}', array('style' => 'oauth_button_style'),array('style' => 'fbbutton_text_style'),$onclick2);
    }

    public function logoutForm(){
        $this->setHeader();
        $this->data->scroll[] = $this->getText('{#click_on_the_button_below_to_logout#}',array( 'style' => 'register-text-step-2'));
        $this->data->scroll[] = $this->getSpacer(50);
        $this->data->scroll[] = $this->getTextbutton('{#logout#}',array('style' => 'button_imate_red','id' => 'logout'));
    }

}