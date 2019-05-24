<?php

class fashionMobileloginSubController extends MobileloginController {

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

        $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png',$this->getVariableId('email'),'{#email#}',$error);
        $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->getVariableId('password'),'{#password#}',false,'password');
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

        $this->getCreateAccountBlock();

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

    public function getCreateAccountBlock() {

        $role = $this->getSavedVariable('role');

        if ( $role == 'influencer' ) {
            return false;
        }

        $content4[] = $this->getTextbutton('{#forgot_password#}',array('id' => 'reset-password-form',
            'background-color' => $this->colors['top_bar_color'],
            'color' => $this->colors['top_bar_text_color'],
            'border-radius' => '4',
            'font-ios' => 'Didot',
            'font-android' => 'Didot',
            'font-size' => '11',
            'height' => '45',
            'width' => '48%'
        ));

        $content4[] = $this->getVerticalSpacer('4%');

        $content4[] = $this->getTextbutton('{#signup_with_email#}', array('action' => 'open-branch','id' => '393933','config' => $this->requireConfigParam('register_branch'),
            'background-color' => $this->colors['top_bar_color'],
            'color' => $this->colors['top_bar_text_color'],
            'border-radius' => '4',
            'font-ios' => 'Didot',
            'font-android' => 'Didot',
            'font-size' => '11',
            'height' => '45',
            'width' => '48%'
        ));

        $this->data->scroll[] = $this->getRow($content4,array('margin' => '10 40 0 40','text-align' =>'center'));
    }
    
}