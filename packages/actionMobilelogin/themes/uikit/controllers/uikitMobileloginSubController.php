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

class uiKitMobileloginSubController extends MobileloginController
{

    public $data;
    public $configobj;
    public $theme;
    public $fields = array('password1' => 'password1', 'password2' => 'password2');

    public function loginForm($error = false)
    {
        $loggedin = $this->getSavedVariable('logged_in');

        if ($loggedin == 1) {
            $this->logoutForm();
            return true;
        }

        if ($this->getConfigParam('only_logout')) {
            $this->data->scroll[] = $this->getImage('uikit_balls_loader.gif');
            //$this->data->scroll[] = $this->getFullPageLoader();
            return true;
        }

        $reg = Appcaching::getGlobalCache($this->playid . '-' . 'registration');

        if ($reg) {
            Yii::app()->cache->set($this->playid . '-' . 'registration', false);
        }

        if ($this->getConfigParam('actionimage2')) {
            $image = $this->getConfigParam('actionimage2');
            $this->data->scroll[] = $this->getRow(array(
                $this->getImage($image),
            ), array(
                'text-align' => 'right',
                'background-color' => $this->color_topbar,
            ));

        } else {
            $image = 'register-dhl-logo.png';
            $this->data->scroll[] = $this->getRow(array(
                $this->getImage($image, array(
                    'width' => '120'
                )),
            ), array(
                'padding' => '15 15 0 15',
                'text-align' => 'right',
                'background-color' => $this->color_topbar,
            ));
        }

        $text = '{#login_header_text#}';

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHeaderImage($this->getLogo()),
            $this->getHeaderText($text)
        ), array(
            'background-color' => $this->color_topbar,
            'text-align' => 'center'
        ));

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->transition = 'none';
        $onclick->action_config = $this->getActionidByPermaname('register');

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getText(strtoupper('{#sign_in#}'), array(
                    'padding' => '23 0 20 0',
                    'text-align' => 'center',
                    'background-color' => '#ffffff',
                    'border-width' => '1',
                    'border-color' => '#fafafa',
                    'font-size' => '14',
                )),
                $this->getSpacer('3', array(
                    'background-color' => $this->color_topbar
                ))
            ), array(
                'width' => '50%',
            )),
            $this->getText(strtoupper('{#sign_up#}'), array(
                'width' => '50%',
                'padding' => '20 0 20 0',
                'text-align' => 'center',
                'background-color' => '#ffffff',
                'border-width' => '1',
                'border-color' => '#fafafa',
                'font-size' => '14',
                'onclick' => $onclick
            ))
        ));

        $mailField = $this->getFieldWithIcon('mail3.png', $this->getVariableId('email'), '{#e-mail#}', $error, 'email');
        if ($error) {
            $mailField->column_content[] = $this->getTextbutton('{#forgot_password#}?', array('id' => 'reset-password-form', 'style' => 'register-text-step-error-forgot-password'));
        }

        $this->data->scroll[] = $mailField;

        $this->data->scroll[] = $this->getSpacer(1, array(
            'background-color' => '#dfdfdf',
            'margin' => '0 20 0 20',
            'opacity' => '0.5'
        ));

        $this->data->scroll[] = $this->getFieldWithIcon('key2.png', $this->getVariableId('password'), '{#password#}', false, 'password', 'do-regular-login');

        $this->data->scroll[] = $this->getSpacer(1, array(
            'background-color' => '#dfdfdf',
            'margin' => '0 20 0 20',
            'opacity' => '0.5'
        ));

        $onclick = new StdClass();
        $onclick->id = 'do-regular-login';
        $onclick->action = 'submit-form-content';

        $this->data->footer[] = $this->getRow(array(
            $this->getText('{#sign_in#}', array(
                'id' => 'id',
                'onclick' => $onclick,
                'margin' => '15 0 15 0',
                'width' => '75%',
                'text-align' => 'center',
                'color' => $this->colors['top_bar_text_color'],
                'background-color' => $this->colors['button_color'],
                'font_weight' => 'bold',
                'font_ios' => 'Frutiger65',
                'font_android' => 'Frutiger65',
                'height' => '50',
                'use_clipping' => '0'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '0 0 0 0'
        ));

        return true;
    }

    public function doLogin()
    {

        $id_email = $this->getVariableId('email');
        $id_password = $this->getVariableId('password');

        $email = strtolower($this->getSubmitVariable($id_email));
        $password = sha1(trim($this->getSubmitVariable($id_password)));

        if (strlen($this->getSubmitVariable($id_password)) > 3) {
            $this->loginmodel->password = $password;
            $this->loginmodel->username = $email;
            $login = $this->loginmodel->doLogin();

            if ($login) {
                $this->playid = $login;
                $this->loadVariables();
                $this->finishLogin(true, true, __LINE__);
            } else {
                $this->loginForm('{#user_not_found_or_password_wrong#}');
            }
        } else {
            $this->loginForm('{#user_not_found_or_password_wrong#}');
        }
    }

    protected function getHeaderImage($image)
    {
        return $this->getImage($image, array(
            'width' => '200',
            'margin' => '20 0 20 0'
        ));
    }

    protected function getHeaderText($text)
    {
        return $this->getText($text, array(
            'color' => '#ffffff',
            'font-size' => '24',
            'text-align' => 'center',
            'margin' => '0 0 50 0',
            'font-weight' => 'bold',
        ));
    }

    public function logoutForm()
    {
        $this->rewriteactionfield('subject', 'LOG OUT');

        $this->data->scroll[] = $this->getRow(array(
            $this->getHeaderImage($this->getLogo())
        ), array(
            'text-align' => 'center'
        ));

        $onclick = new stdClass();
        $onclick->action = 'logout';

        $clicker[] = $onclick;
        $clicker[] = $this->getOnclick('id', false, 'logout');

        $string = $this->localizationComponent->smartLocalize('{#logout#}');

        $this->data->footer[] = $this->getRow(array(
            $this->getText($string, array(
                'onclick' => $onclick,
                'margin' => '15 0 15 0',
                'width' => '75%',
                'text-align' => 'center',
                'color' => $this->colors['top_bar_text_color'],
                'background-color' => $this->colors['button_color'],
                'font_weight' => 'bold',
                'font_ios' => 'Frutiger65',
                'font_android' => 'Frutiger65',
                'height' => '50',
                'use_clipping' => '0'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '15 0 0 0'
        ));
    }

    public function lostPassForm($error = false)
    {
        $text = '{#login_header_text#}';

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHeaderImage($this->getLogo()),
            $this->getHeaderText($text)
        ), array(
            'background-color' => $this->color_topbar,
            'text-align' => 'center'
        ));

        $this->data->scroll[] = $this->getFieldWithIcon('mail3.png', 'email-to-reset', 'Email', $error, 'text', 'reset-password', 'email');

        if ($error) {
            $this->data->scroll[] = $this->getSpacer(10);
        }

        $this->data->scroll[] = $this->getSpacer(1, array(
            'background-color' => '#dfdfdf',
            'margin' => '0 20 0 20',
            'opacity' => '0.5'
        ));

        $onclick = new StdClass();
        $onclick->id = 'reset-password';
        $onclick->action = 'submit-form-content';

        $this->data->footer[] = $this->getRow(array(
            $this->getText('{#reset_password#}', array(
                'onclick' => $onclick,
                'margin' => '15 0 15 0',
                'width' => '75%',
                'text-align' => 'center',
                'color' => $this->colors['top_bar_text_color'],
                'background-color' => $this->colors['button_color'],
                'font_weight' => 'bold',
                'font_ios' => 'Frutiger65',
                'font_android' => 'Frutiger65',
                'height' => '50',
                'use_clipping' => '0'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '15 0 0 0'
        ));
        $this->data->footer[] = $this->getRow(array(
            $this->getTextbutton('{#cancel#}', array('style' => 'button_imate_red', 'id' => 'login-form'))),
            array(
                'text-align' => 'center',
                'margin' => '15 0 15 0'
            ));

    }

    public function getCodeEntryForm()
    {
        $text = '{#login_header_text#}';

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHeaderImage($this->getLogo()),
            $this->getHeaderText($text)
        ), array(
            'background-color' => $this->color_topbar,
            'text-align' => 'center'
        ));

        $this->data->scroll[] = $this->getText('{#password_code_hint#}', array('style' => 'mobile-login-general-text'));
        $this->data->scroll[] = $this->getFieldWithIcon('mail3.png', $this->getVariableId('password_code'),
            '{#enter_code_from_email#}', false, 'text', false, 'lowercase');

        $params['margin'] = '10 42 0 42';
        $params['align'] = 'center';
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->footer[] = $this->getRow(array(
            $this->getTextbutton('{#enter_code#}', array('style' => 'button-style-reset', 'id' => 'pw-reset'))),
            array(
                'text-align' => 'center',
                'margin' => '15 0 15 0'
            ));
        $this->data->footer[] = $this->getRow(array(
            $this->getTextbutton('{#resend_password_reset#}', array('style' => 'button-style-reset', 'id' => 'reset-password'))),
            array(
                'text-align' => 'center',
                'margin' => '15 0 15 0'
            ));
        $this->data->footer[] = $this->getRow(array(
            $this->getTextbutton('{#cancel#}', array('style' => 'button_imate_red', 'id' => 'cancel-pass-reset'))),
            array(
                'text-align' => 'center',
                'margin' => '15 0 15 0'
            ));
    }

    public function validateCode()
    {
        $submit = $this->getSubmitVariable($this->getVariableId('password_code'));

        if ($submit == $this->getSavedVariable('pass_reset') OR $this->menuid == 'validate-new-pass') {

            if ($this->menuid == 'validate-new-pass') {
                
                $error = $this->checkForError('password_validity', '4 {#chars_at_least#}');
                $error2 = $this->checkForError('password_match', 'Passwords don\'t match');

                if ($error == false AND $error2 == false) {
                    $pass = $this->getSubmitVariable($this->fields['password1']);

                    if ($pass) {
                        /* save password */
                        $email = $this->getSavedVariable('email');
                        $play = $this->loginmodel->findUserWithoutPassword($email);
                        $this->saveRemoteVariable('password', sha1(trim($pass)), $play);
                        $this->saveVariable('reset_in_progress', 0);
                        $this->saveVariable('pass_reset', 0);
                        $this->loginForm('{#password_updated_please_login#}');
                        return true;
                    }
                }
            } else {
                $error = false;
                $error2 = false;
            }

            $text = '{#login_header_text#}';

            $this->data->scroll[] = $this->getColumn(array(
                $this->getHeaderImage($this->getLogo()),
                $this->getHeaderText($text)
            ), array(
                'background-color' => $this->color_topbar,
                'text-align' => 'center'
            ));

            $this->data->scroll[] = $this->getText('{#choose_new_password#}', array('style' => 'mobile-login-general-text'));
            $this->data->scroll[] = $this->getFieldWithIcon('key2.png', $this->fields['password1'], '{#password#} (4 {#chars_at_least#})', $error, 'password');
            $this->data->scroll[] = $this->getFieldWithIcon('key2.png', $this->fields['password2'], '{#repeat_password#}', $error2, 'password', 'mobilereg_do_registration');
            $this->data->scroll[] = $this->getSpacer('5');

            $this->data->footer[] = $this->getRow(array(
                $this->getTextbutton('{#save_new_password#}', array('style' => 'button-style-reset', 'id' => 'validate-new-pass'))),
                array(
                    'text-align' => 'center',
                    'margin' => '15 0 15 0'
                ));

            return true;
        } else {
            $this->getCodeEntryForm();
            $this->data->scroll[] = $this->getText('{#wrong_code#}', array(
                'style' => 'register-text-step-error',
            ));
            return true;
        }

    }

    public function doPassReset()
    {
        $user_mail = $this->getSubmitVariable('email-to-reset');

        if (!$user_mail OR $user_mail == '0') {
            $this->lostPassForm('{#check_email#}');
            return true;
        }

        $validator = new CEmailValidator;
        $validator->checkMX = true;

        if (!$validator->validateValue($user_mail)) {
            $this->lostPassForm('{#not_a_valid_email#}');
            return true;
        }

        $obj = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->getVariableId('email'), 'value' => $user_mail));

        if (!is_object($obj)) {
            $this->lostPassForm('{#email_not_found#}');
            return true;
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($obj->play_id);

        if (!isset($vars['password']) OR !$vars['password']) {
            $this->lostPassForm('{#logged_in_with_another_service_cant_reset_password#}');
            return true;
        } elseif (isset($vars['fb_id']) AND $vars['fb_id']) {
            if (!isset($vars['password']) OR !$vars['password']) {
                $this->lostPassForm('{#logged_in_with_another_service_cant_reset_password#}');
                return true;
            }
        }

        /* very specific case where user has logged in with one account & then does a reset for another account */
        $code = Helper::generateShortcode();

        $this->saveVariable('email', $user_mail);
        $this->saveVariable('pass_reset', $code);

        $subject = $this->getConfigParam('email_subject', '!MPROVE - Reset Password');

        $body = "Dear " . $vars['firstname'] . ",<br><br>";
        $body .= $this->localizationComponent->smartLocalize('{#we_received_a_request_to_change_the_password#}.');
        $body .= "<br /><br />";
        $body .= $this->localizationComponent->smartLocalize('{#if_you_want_to_set_a_new_password_please_enter_the_following_code_in_the_app_on_your_mobile#}: ');
        $body .= "<br /><br />";
        $body .= $code;
        $body .= "<br /><br />";

        $body .= 'Kind regards';
        $body .= "<br />";

        if ($footer_text = $this->getConfigParam('email_footer_text')) {
            $body .= $footer_text;
        } else {
            $body .= 'Your Continuous Improvement / First Choice team' . "<br><br>";
            $body .= "Mail: dgf-firstchoice@dhl.com";
        }

        Aenotification::addUserEmail($this->playid, $subject, $body, $this->gid, $user_mail);

        $this->passResetCode();
    }

    public function getLogo()
    {

        if ($this->getConfigParam('actionimage1')) {
            return $this->getConfigParam('actionimage1');
        }

        return 'dhl_app_icon.png';
    }

    public function loggingInHeader()
    {
        $this->data->scroll[] = $this->getFullPageLoader();
        $this->data->scroll[] = $this->getText('{#logging_in#}', array(
            'text-align' => 'center',
            'padding' => '20 20 20 20',
            'color' => '#000000',
        ));
    }

}