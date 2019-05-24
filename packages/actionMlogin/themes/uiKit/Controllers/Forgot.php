<?php


namespace packages\actionMlogin\themes\uiKit\Controllers;


use Bootstrap\Controllers\BootstrapController;
use packages\actionMlogin\themes\uiKit\Models\Model as ArticleModel;


class Forgot extends BootstrapController
{
    public $view;
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        $data = [];

        if($this->model->getSavedVariable('code')){
            return ['Resetpassword', $data];
        }

        $data['finishLogin'] = 0;
        return ['ForgotPassword',$data];
    }

    public function actionSendCode()
    {
        $user_mail = $this->model->getSubmittedVariableByName('email-to-reset') ;
        $data['errors'] = '';

        $data['finishLogin'] = 0;
        if ( !$user_mail OR $user_mail == '0') {
            $data['errors'] = '{#check_email#}';
            return ['ForgotPassword', $data];
        }

        $user_mail = trim(strtolower($user_mail));

        if(!$this->model->validateEmail($user_mail)){
            $data['errors'] = '{#not_a_valid_email#}';
            return ['ForgotPassword', $data];
        }

        $this->model->saveVariable('email', $user_mail);

        $obj = \AeplayVariable::model()->findByAttributes(array('variable_id' => $this->model->getVariableId('email'),'value' => $user_mail));

        if(!is_object($obj)){
            $data['errors'] = '{#email_not_found#}';
            return ['ForgotPassword', $data];
        }

        $vars = \AeplayVariable::getArrayOfPlayvariables($obj->play_id);

        if(!isset($vars['password']) OR !$vars['password']){
            $data['errors'] = '{#logged_in_with_another_service_cant_reset_password#}';
            return ['ForgotPassword', $data];
        } elseif(isset($vars['fb_id']) AND $vars['fb_id']){
            if(!isset($vars['password']) OR !$vars['password']){
                $data['errors'] = '{#logged_in_with_another_service_cant_reset_password#}';
                return ['ForgotPassword', $data];
            }
        }

        $code = \Helper::generateShortcode();

        $this->model->saveVariable('code', $code);

        $from = isset($this->model->actionobj->gamename) ? $this->model->actionobj->gamename : 'Appzio';

        $body = $this->model->localize('{#we_received_a_request_to_change_the_password#}');
        $body .= "<br /><br />";
        $body .= $this->model->localize('{#if_you_want_to_change_your_password_please_enter_the_following_code_to_reset_the_application_on_your_mobile#}: ');
        $body .= "<br /><br />";
        $body .= $this->model->localize('{#reset_password#}: ') . $code;
        $body .= "<br /><br />";

        \Aenotification::addUserEmail(
            $this->playid,
            $from . ' - password reset',
            $body,
            $this->model->actionobj->game_id,
            $user_mail
        );

        return ['Resetpassword', $data];
    }

    public function actionResetPassword()
    {
        $code = $this->model->getSubmittedVariableByName('temp_code');

        if(!empty($code) && $code == $this->model->getSavedVariable('code')) {
            $password = $this->model->getSubmittedVariableByName('password');
            $confirmPassword = $this->model->getSubmittedVariableByName('confirm_password');
            $data['errors'] = $this->model->validatePassword($password, $confirmPassword);
            if($data['errors'])
                return ['ResetPassword', $data];

            $email = $this->model->getSavedVariable('email');
            $play = $this->model->findUserWithoutPassword($email);
            $this->model->foreignVariableSave('password', sha1(strtolower(trim($password))), $play);
            $this->model->saveVariable('reset_in_progress', 0);
            $this->model->saveVariable('pass_reset', 0);
            $data['errors'] = '{#please_enter_your_new_password#}';
            $data['finishLogin'] = 0;
            $data['change_tab'] = 1;
            return ['Login', $data];
        }

        $this->model->validation_errors['temp_code'] = '{#not_a_valid_code#}';
        $data['errors'] = '{#not_a_valid_code#}';
        return ['ResetPassword', $data];
    }
}
