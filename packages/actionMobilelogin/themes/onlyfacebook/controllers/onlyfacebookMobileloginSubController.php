<?php

class onlyfacebookMobileloginSubController extends MobileloginController {

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
        $this->data->scroll[] = $this->getGifitSigning();

        $this->data->footer[] = $this->getRow(array(
            $this->getText( '{#by_signing_in_you_agree_to_our#} ', array( 'color' => '#ffffff', 'font-size' => '12' ) ),
            $this->getText( '{#terms_of_service#}', array( 'color' => '#a9ddee', 'font-size' => '12', 'onclick' => $onclick ) ),
        ), array( 'text-align' => 'center', 'margin' => '10 10 10 10' ));

        //$this->data->scroll[] = $this->getImage( 'fb-button.png', array( 'priority' => 1, 'margin' => '0 20 5 20', 'onclick' => $fbclick ) );

    }

    public function getGifitSigning(){

        $onclick1 = new StdClass();
        $onclick1->action = 'submit-form-content';
        $onclick1->id = 'show-loader';

        $onclick2 = new StdClass();
        $onclick2->id = 'insta';
        $onclick2->action = 'open-action';
        $onclick2->action_config = $this->getConfigParam('corporate_login');
        $onclick2->sync_close = 1;

        return $this->getButtonWithIcon('gifit-logo.png', 'insta', '{#corporate_sign_in#}', array('style' => 'instagram_button_style'),array('style' => 'instagram_text_style'),array($onclick1,$onclick2));
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

        $image_styles['width'] = $this->screen_width;
        $image_styles['priority'] = 1;
        $navi_styles['margin'] = '0 0 0 0';
        $navi_styles['text-align'] = 'center';

        $totalcount = count($images);
        $items = array();

        foreach ($images as $i => $image) {
            $scroll = array();
            $scroll[] = $this->getImage($image, $image_styles);
            //$scroll[] = $this->getSwipeNavi($totalcount, ($i+1), $navi_styles);
            $items[] = $this->getColumn($scroll, array());
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getSwipearea($items),
            ), array( 'margin' => '0 0 0 0' )),
        ), array( 'margin' => '0 0 0 0' ));

    }

}