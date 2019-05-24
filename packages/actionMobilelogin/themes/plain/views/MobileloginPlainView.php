<?php

class MobileloginPlainView extends MobileloginPlainController
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
        $this->data->scroll[] = $this->getSpacer('40');

        $fbclick = new StdClass();

        if( $this->getConfigParam('instagram_enabled') AND $this->getConfigParam('instagram_login_provider')) {
            $fbclick->action = 'open-action';
            $fbclick->sync_open = 1;
            $fbclick->action_config = $this->getActionidByPermaname('instagram');

            $this->data->scroll[] = $this->getButtonWithIcon('white_insta.png', 'do-instagram-login', '{#login_with_instagram#}', array(
                'style' => 'instagram_button_style',
                'action' => 'open-action',
                'sync_open' => true,
            ), array(
                'color' => '#ffffff',
                'font-size' => '15',
            ), $fbclick);
        }

        $fbclick = new StdClass();

        $this->data->scroll[] = $this->getSpacer('10');

        if( $this->getConfigParam('facebook_enabled') ) {


            if (!$loggedin AND $this->getSavedVariable('fb_token')) {
                $fbclick->id = 'do-fb-login-alreadylogged';
                $fbclick->action = 'submit-form-content';
                $fbclick->read_permissions = ['email', 'public_profile'];

            } else {
                $fbclick->id = 'do-fb-login';
                $fbclick->action = 'fb-login';
                $fbclick->read_permissions = ['email', 'public_profile'];
                $fbclick->sync_open = 1;
            }

            $this->data->scroll[] = $this->getButtonWithIcon('fb-icon-new.png', 'do-fb-login', '{#login_with_facebook#}', array(
                'style' => 'facebook_button_style',
                'action' => 'fb-login',
                'sync_open' => true,

            ), array(
                'color' => '#FFFFFF',
                'font-size' => '15',
            ), $fbclick);

        }

        $this->data->footer[] = $this->getText('{#we_wont_post_anything_on_your_wall#}', array(
            'text-align' => 'center',
            'color' => '#000000',
            'font-size' => '12',
            'margin' => '10 0 10 0'
        ));

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname('eula');
        $onclick->open_popup = '1';

        $this->data->footer[] = $this->getColumn(array(
            $this->getText('{#by_registering_you_agree_to_the#}', array(
                'text-align' => 'center',
                'font-size' => '12',
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

        $this->data->scroll[] = $this->getText('{#login_slogan#}', array(
            'text-align' => 'center',
            'color' => '#000000',
            'font-size' => '27',
            'font-ios' => 'Lato-Light',
            'font-android' => 'Lato-Light',
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