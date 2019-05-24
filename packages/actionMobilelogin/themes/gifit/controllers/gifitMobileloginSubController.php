<?php

class gifitMobileloginSubController extends MobileloginController {

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

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = $this->getConfigParam('terms_popup');
        $onclick->config = $this->getConfigParam('terms_popup');
        $onclick->action_config = $this->getConfigParam('terms_popup');
        $onclick->open_popup = '1';


        $fbclick = new StdClass();

        // if($this->fblogin === true AND !$loggedin AND $this->getSavedVariable('fb_token')){
        if(!$loggedin AND $this->getSavedVariable('fb_token')){
            $fbclick->id = 'do-fb-login-alreadylogged';
            $fbclick->action = 'submit-form-content';
        } else {
            $fbclick1 = new stdClass();
            $fbclick1->id = 'do-fb-login';
            $fbclick1->action = 'fb-login';
            $fbclick1->sync_open = 1;

            $fbclick2 = new stdClass();
            $fbclick2->action = 'ask-location';

            $fbclick = array($fbclick1,$fbclick2);

        }

        $this->data->scroll[] = $this->getFacebookSignInButton('fb');
        $this->data->scroll[] = $this->getRegisterButton();
        $this->data->scroll[] = $this->getSpacer(1);
        $this->data->scroll[] = $this->getGifitSigning();

        $this->data->footer[] = $this->getRow(array(
            $this->getText( '{#by_signing_in_you_agree_to_our#} ', array( 'color' => '#ffffff', 'font-size' => '12' ) ),
            $this->getText( '{#terms_of_service#}', array( 'color' => '#a9ddee', 'font-size' => '12', 'onclick' => $onclick ) ),
        ), array( 'text-align' => 'center', 'margin' => '10 10 10 10' ));

        //$this->data->scroll[] = $this->getImage( 'fb-button.png', array( 'priority' => 1, 'margin' => '0 20 5 20', 'onclick' => $fbclick ) );

    }


    public function loggingInHeader(){
        $this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
        $this->data->scroll[] = $this->getText('{#logging_in#}',array('style' => 'gifit-titletext-header'));
        $this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
        $this->data->scroll[] = $this->getFullPageLoader();
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

    public function getRegisterButton(){

        $onclick2 = new stdClass();
        $onclick2->id = 'insta';
        $onclick2->action = 'open-branch';
        $onclick2->action_config = $this->getConfigParam('register_branch');
        $onclick2->sync_close = 1;

        return $this->getTextbutton('{#join_a_team_or_create_a_new_one#} ({#register#})', array('style' => 'instagram_button_style','onclick' => $onclick2,'id' => 'login','small_text' => true));
    }


    public function getGifitSigning(){

        $onclick2 = new stdClass();
        $onclick2->id = 'corporate-login';
        $onclick2->action = 'open-action';
        $onclick2->action_config = $this->getConfigParam('corporate_login');
        $onclick2->sync_close = 1;

        return $this->getTextbutton('{#login_with_email#}', array('style' => 'instagram_button_style','onclick' => $onclick2,'id' => 'login','small_text' => true));
    }


    public function setHeader($padding=false){

        $width = $this->screen_width;
        $height = $this->screen_width;

        $img1 = $this->getImageFileName('intro1gifit2.png',array('priority' => '1'));
        $img2 = $this->getImageFileName('intro2gifit.png',array('priority' => '1'));
        $img3 = $this->getImageFileName('intro3gifit.png',array('priority' => '1'));

        $items[] = $this->getColumn($this->intro1(),array('background-image' => $img1,
            'width' => $width,'height' => $height,'background-size' => 'cover','vertical-align' => 'top'));
        $items[] = $this->getColumn($this->intro2(),array('background-image' => $img2,
            'width' => $width,'height' => $height,'background-size' => 'cover','vertical-align' => 'top'));
        $items[] = $this->getColumn($this->intro3(),array('background-image' => $img3,
            'width' => $width,'height' => $height,'background-size' => 'cover','vertical-align' => 'top'));

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getSwipearea($items),
            ), array( 'margin' => '0 0 0 0' )),
        ), array( 'margin' => '0 0 0 0' ));

        $this->data->scroll[] = $this->getImage('powered-by.png');
        $this->data->scroll[] = $this->getSpacer(20);

    }

    public function intro1(){
        $col[] = $this->getSpacer($this->screen_width*0.6);
        $col[] = $this->getText('{#be_cool#}. {#show_you_care#}.',array('style' => 'gifit-titletext'));
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText('{#share_feedback#}.',array('style' => 'gifit-titletext'));
        return $col;
    }

    public function intro2(){
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText('{#how_it_works#}?',array('style' => 'gifit-titletext'));
        $col[] = $this->getSpacer(20);
        $col[] = $this->getText('{#gifit_intro_explanation#}',array('style' => 'gifit-title-explanation'));
        return $col;
    }

    public function intro3(){
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText('{#about_teams#}',array('style' => 'gifit-titletext'));
        $col[] = $this->getSpacer(20);
        $col[] = $this->getText('{#gifit_team_explanation#}',array('style' => 'gifit-title-explanation'));
        return $col;
    }



}