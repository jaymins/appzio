<?php

class dittoMobileloginSubController extends MobileloginController {

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

        $this->data->scroll[] = $this->getRow(array(
            $this->getText( 'By signing in, you agree to our ', array( 'color' => '#ffffff', 'font-size' => '14' ) ),
            $this->getText( 'terms of service', array( 'color' => '#009deb', 'font-size' => '14', 'onclick' => $onclick ) ),
        ), array( 'text-align' => 'center', 'margin' => '10 10 10 10' ));

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

        $this->data->scroll[] = $this->getImage( 'fb-button.png', array( 'priority' => 1, 'margin' => '0 20 5 20', 'onclick' => $fbclick ) );

        /*
        $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png',$this->getVariableId('email'),'{#email#}',$error);
        $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->getVariableId('password'),'{#password#}',false,'password');
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->scroll[] = $this->getTextbutton('{#login#}',array('style' => 'general_button_style','id' => 'do-regular-login'));

        $content4[] = $this->getTextbutton('{#forgot_password#}',array('id' => 'reset-password-form','style' => 'text_link'));
        $content4[] = $this->getTextbutton('{#signup#}', array('id' => '393933', 'action' => 'open-branch', 'config' => $this->requireConfigParam('register_branch'), 'style' => 'text_link_right'));
        $this->data->scroll[] = $this->getRow($content4,array('margin' => '20 42 0 42'));
        */
    }

    public function setHeader($padding=false){

        $images = array(
            'image-1.png', 'image-2.png', 'image-3.png', 'image-4.png'
        );
        
        $image_styles['imgwidth'] = '750';
        $image_styles['imgheight'] = '1103';
        $image_styles['imgcrop'] = 'yes';
        $image_styles['width'] = $this->screen_width;
        $image_styles['priority'] = 1;
        // $image_styles['height'] = $this->screen_width;
        //$image_styles['not_to_assetlist']  = true;

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
    
}