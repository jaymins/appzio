<?php

class MobileloginDeseeView extends MobileloginDeseeController
{
    public function loginForm($error = false)
    {
        $loggedin = $this->getSavedVariable('logged_in');

        if ($loggedin) {
            $this->logoutForm();
            return true;
        }

        if ($this->getConfigParam('only_logout')) {
            $this->data->scroll[] = $this->getFullPageLoader();
            return true;
        }

        $this->setHeader();

        $fbclick = new StdClass();

        if ( !$loggedin AND $this->getSavedVariable('fb_token') ) {
            $fbclick->id = 'do-fb-login-alreadylogged';
            $fbclick->action = 'submit-form-content';
            $fbclick->read_permissions = ['email','public_profile'];

        } else {
            $fbclick->id = 'do-fb-login';
            $fbclick->action = 'fb-login';
            $fbclick->read_permissions = ['email','public_profile'];
            $fbclick->sync_open = 1;
        }
        $this->data->footer[] = $this->getButtonWithIcon('fb-icon-new.png', 'do-fb-login', 'Continue with Facebook', array(
            'style' => 'facebook_button_style',
            'action' => 'fb-login',
            'sync_open' => true,
        ), array(
            'color' => '#FFFFFF',
            'font-size' => '15',
        ), $fbclick);
        $this->data->footer[] = $this->getText('We\'ll never post anything on your wall', array(
            'text-align' => 'center',
            'color' => '#000000',
            'font-size' => '13',
            'margin' => '10 0 10 0'
        ));

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname('eula');
        $onclick->open_popup = '1';

        $this->data->footer[] = $this->getColumn(array(
            $this->getText('{#by_registering_you_agree_to_the#}', array(
                'text-align' => 'center',
                'font-size' => '14',
            )),
            $this->getText('{#terms_and_conditions#}', array(
                'style' => 'terms_and_conditions_bold',
                'onclick' => $onclick
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '0 0 10 0'
        ));

        return true;
    }

    public function setHeader($padding = false)
    {
        $image = $this->getHeaderImage();
        $height = $this->getHeight();

        $this->data->scroll[] = $this->getSpacer($height);

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage($image, array(
                'width' => '200'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '100 0 0 0'
        ));

        $this->data->scroll[] = $this->getText('Connecting Desees worldwide', array(
            'text-align' => 'center',
            'color' => '#000000',
            'margin' => '20 0 0 0',
        ));
    }

    public function logoutForm(){

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage('desee-logo.png', array(
                'width' => '200'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '120 0 0 0'
        ));

        $this->data->scroll[] = $this->getText('{#click_on_the_button_below_to_logout#}',array(
            'style' => 'mobile-login-general-text'
        ));

        $this->data->footer[] = $this->getTextbutton('{#logout#}',array('style' => 'desee_general_button_style_footer','id' => 'logout'));
    }

}