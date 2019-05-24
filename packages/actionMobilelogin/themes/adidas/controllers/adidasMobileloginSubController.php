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

class adidasMobileloginSubController extends MobileloginController
{

    public $data;
    public $configobj;
    public $theme;
    public $fields = array('password1' => 'password1', 'password2' => 'password2');

    public function loginForm($error = false)
    {
        $this->rewriteActionConfigField('background_color', '#ffffff');

        $loggedin = $this->getSavedVariable('logged_in');

        if ($loggedin == 1) {
            $this->logoutForm();
            return true;
        }

        if ($this->getConfigParam('only_logout')) {
            $this->data->scroll[] = $this->getFullPageLoader();
            return true;
        }

        $reg = Appcaching::getGlobalCache($this->playid . '-' . 'registration');

        if ($reg) {
            Yii::app()->cache->set($this->playid . '-' . 'registration', false);
        }


        if ($this->getConfigParam('actionimage1')) {
            $image_file = $this->getConfigParam('actionimage1');
        } elseif ($this->getImageFileName('tatjack_logo_2.png')) {
            $image_file = 'tatjack_logo_2.png';
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage($image_file, array(
                'width' => '100%',
                'margin' => '0 0 20 0'
            ))
        ), array(
            'text-align' => 'center'
        ));



        $this->data->scroll[] = $this->getFieldWithIcon('003-mail.png', $this->getVariableId('email'), '{#email#}', $error, 'email');
        $this->data->scroll[] = $this->getFieldWithIcon('002-security.png', $this->getVariableId('password'), '{#password#}', false, 'password', 'do-regular-login');
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->scroll[] = $this->getRow(array(
            $this->getTextbutton(strtoupper('{#sign_in#}'), array(
                'id' => 'do-regular-login',
                'style' => 'login_button'
            ))
        ), array(
            'text-align' => 'center'
        ));

//        $this->data->scroll[] = $this->getRow(array(
//            $this->getTextbutton('{#forgot_password#}?', array('id' => 'reset-password-form',
//                'style' => 'forgotten_password_button'
//            ))
//        ), array(
//            'text-align' => 'center'
//        ));

        $fbclick = new StdClass();
        if (!$loggedin AND $this->getSavedVariable('fb_token')) {
            $fbclick->id = 'do-fb-login-alreadylogged';
            $fbclick->action = 'submit-form-content';
        } else {
            $fbclick->id = 'do-fb-login';
            $fbclick->action = 'fb-login';
            $fbclick->sync_open = 1;
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getButtonWithIcon('06-facebook-512.png', 'do-fb-login', 'Continue with Facebook', array(
                'style' => 'fb_login_button',
                'action' => 'fb-login',
                'sync_open' => true,
            ), array(
                'color' => '#3d5b97',
                'font-size' => '16',
            ), $fbclick)
        ), array(
            'text-align' => 'center'
        ));

        $onclick = new StdClass();
        $onclick->action = 'open-branch';
        $onclick->action_config = $this->getConfigParam('register_branch');

        $content4[] = $this->getText('{#dont_have_an_accout#}? ' . '{#sign_up#}', array(
            'color' => '#000000',
            'text-align' => 'center',
            'onclick' => $onclick
        ));

        $this->data->footer[] = $this->getColumn($content4, array(
            'margin' => '0 40 20 40',
            'text-align' => 'center'
        ));

    }

    public function logoutForm()
    {
//        $this->rewriteActionConfigField('background_color', '#edac34');

        $this->setHeader();
        $this->data->scroll[] = $this->getSpacer(50);

        $onclick = new stdClass();
        $onclick->action = 'logout';

        $clicker[] = $onclick;
        $clicker[] = $this->getOnclick('id', false, 'logout');

        $this->data->footer[] = $this->getText(strtoupper('{#sign_out#}'), array(
            'onclick' => $onclick,
            'id' => 'id',
            'border-color' => '#656B6F',
            'color' => '#656B6F',
            'margin' => '20 80 20 80',
            'border-radius' => '19',
            'height' => '40',
            'font-size' => '16',
            'text-align' => 'center',
            'vertical-align' => 'middle',
        ));
    }

    public function lostPassForm($error = false)
    {
        $this->rewriteActionConfigField('background_color', '#ffffff');

        $this->setHeader();
        $this->data->scroll[] = $this->getFieldWithIcon('envelope_white.png', 'email-to-reset', 'Email', $error, 'email');

        $this->data->scroll[] = $this->getRow(array(
            $this->getTextbutton('{#reset_password#}', array('style' => 'login_button', 'id' => 'reset-password'))
        ), array(
            'text-align' => 'center'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getTextbutton('{#cancel#}', array('style' => 'cancel_button', 'id' => 'login-form'))
        ), array(
            'text-align' => 'center'
        ));
    }

    public function setHeader($padding = false)
    {
        if ($padding) {
            $height = $padding;
        } elseif ($this->aspect_ratio > 0.57) {
            $height = 20;
        } else {
            $height = 45;
        }

        if ($this->getConfigParam('actionimage1')) {
            $image_file = $this->getConfigParam('actionimage1');
        } elseif ($this->getImageFileName('tatjack_logo_2.png')) {
            $image_file = 'tatjack_logo_2.png';
        }

        $this->data->scroll[] = $this->getSpacer($height);

        if (isset($image_file)) {
            $this->data->scroll[] = $this->getRow(array(
                $this->getImage($image_file, array(
                    'width' => 'auto',
                    'margin' => '30 0 10 0',
                ))
            ), array(
                'text-align' => 'center'
            ));
        }

        $this->data->scroll[] = $this->getSpacer('20');
    }

    public function getCodeEntryForm()
    {
        $this->setHeader();
        $this->data->scroll[] = $this->getFieldWithIcon('envelope_white.png', $this->getVariableId('password_code'),
            '{#enter_code_from_email#}', false, 'text', false, 'lowercase');

        $params['margin'] = '10 42 0 42';
        $params['align'] = 'center';
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->scroll[] = $this->getTextbutton('{#enter_code#}', array('style' => 'general_button_style', 'id' => 'pw-reset'));
        $this->data->scroll[] = $this->getTextbutton('{#resend_password_reset#}', array('style' => 'general_button_style', 'id' => 'reset-password'));
        $this->data->scroll[] = $this->getTextbutton('{#cancel#}', array('style' => 'button_imate_red', 'id' => 'cancel-pass-reset'));
    }
}