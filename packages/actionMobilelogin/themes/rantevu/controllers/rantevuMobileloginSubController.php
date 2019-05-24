<?php

class rantevuMobileloginSubController extends MobileloginController {

	public function rantevu(){

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

            case 'reset-password-form':
                $this->lostPassForm();
                break;

            case 'login-form':
                $this->loginForm();
                break;

            default:
                $this->loginForm();
                break;
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
        $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->getVariableId('password'),'{#password#}',false,'password');
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->scroll[] = $this->getTextbutton('{#login#}',array('style' => 'general_button_style','id' => 'do-regular-login'));

        if( $this->getConfigParam('facebook_enabled')){
            if($this->fblogin === true AND !$loggedin){
                $this->data->scroll[] = $this->getFacebookSignInButton('do-fb-login-alreadylogged',true);
            } else {
                $this->data->scroll[] = $this->getFacebookSignInButton('do-fb-login');
            }
        }
        
        $this->data->scroll[] = $this->getTextbutton('{#forgot_password#}',array('id' => 'reset-password-form','style' => 'text_link'));

        $this->data->scroll[] = $this->getTextbutton('{#signup#}', array('action' => 'open-branch','id' => '393933','config' => $this->requireConfigParam('register_branch'),'style' => 'text_link'));
    }
    
}