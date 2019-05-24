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

class gaybffMobileloginSubController extends MobileloginController {

    public $data;
    public $configobj;
    public $theme;
    public $fields = array('password1' => 'password1', 'password2' => 'password2');

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

        $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png', $this->getVariableId('email'), '{#email#}', $error);
        $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png', $this->getVariableId('password'), '{#password#}', false, 'password', 'do-regular-login');
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
            'background-color' => '#1825aa',
            'color' => $this->colors['top_bar_text_color'],
            'border-radius' => '4',
            'font-size' => '11',
            'height' => '45',
            'width' => '48%'
        ));
        $content4[] = $this->getVerticalSpacer('4%');

        $content4[] = $this->getTextbutton('{#signup_with_email#}', array('action' => 'open-branch','id' => '393933','config' => $this->requireConfigParam('register_branch'),
            'background-color' => '#8c189b',
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

}