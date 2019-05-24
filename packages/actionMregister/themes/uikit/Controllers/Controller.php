<?php

namespace packages\actionMregister\themes\uikit\controllers;

use packages\actionMregister\themes\uikit\Models\Model as ArticleModel;
use packages\actionMregister\themes\uikit\Views\Main;
use packages\actionMregister\themes\uikit\Views\View as ArticleView;

class Controller extends \packages\actionMregister\Controllers\Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        /* if user has already completed the first phase, move to phase 2 */
        if ($this->model->sessionGet('reg_phase') == 2) {
            return $this->actionPagetwo();
        }

        $data['fieldlist'] = $this->model->getFieldlist();
        $data['current_country'] = $this->model->getCountry();
        $data['mode'] = 'show';

        /* if user has clicked the signuop, we will first validate
        and then save the data. validation errors are also available to views and components. */
        if ($this->getMenuId() == 'signup') {
            $this->model->validatePage1();

            if (empty($this->model->validation_errors)) {
                /* if validation succeeds, we save data to variables and move user to page 2*/
                $this->model->savePage1();

                if ($this->model->getConfigParam('dont_require_email_code')) {
                    $this->model->closeLogin();
                    $data['mode'] = 'close';
                    return ['Complete', $data];
                } else {
                    $this->sendAuthenticationCodeEmail();
                    $data = \ThirdpartyServices::geoAddressTranslation($this->model->getSavedVariable('lat'), $this->model->getSavedVariable('lon'), $this->model->appid);
                    $this->model->saveVariable('current_country', $data['country']);
                    $this->model->sessionSet('reg_phase', 2);
                    return ['Pagetwo', $data];
                }
            }
        }

        return ['View', $data];
    }

    public function actionPagetwo()
    {

        $submittedCode = $this->model->getSubmittedVariableByName('code');
        $authenticationCode = $this->model->getSavedVariable('authentication_code');

        /* no validation here */
        if ($this->getMenuId() == 'done') {

            if ($submittedCode != $authenticationCode) {
                $this->model->validation_errors['code'] = '{#invalid_authentication_code#}';

                return ['Pagetwo', []];
            }

            $this->model->closeLogin();
            $data['mode'] = 'close';
            return ['Complete', $data];
        }

        return ['Pagetwo', []];
    }

    protected function sendAuthenticationCodeEmail()
    {
        $code = rand(1000, 9999);
        $this->model->saveVariable('authentication_code', $code);

        $email = $this->model->getSavedVariable('email');
        $subject = $this->model->getConfigParam('mreg_email_subject') ? $this->model->getConfigParam('mreg_email_subject') : '!MPROVE - Your Registration Code';

        if ($this->model->getConfigParam('mreg_email_body')) {
            $body = $this->model->getConfigParam('mreg_email_body');
            $body = str_replace('{code}', $code, $body);
            $body = str_replace('{firstname}', $this->model->getSavedVariable('firstname'), $body);
        } else {
            $body = 'Dear ' . $this->model->getSavedVariable('firstname') . ",<br><br>";
            $body .= 'Welcome to !MPROVE, the mobile app to support your Continuous Improvement journey.' . "<br><br>";
            $body .= 'To complete your registration, please enter the following code when prompted in the app on your mobile:' . "<br>";
            $body .= $code . "<br><br>";
            $body .= 'We hope you enjoy the app experience and it helps you getting a little bit better Everyday.' . "<br><br>";
            $body .= 'Kind regards' . "<br>";
            $body .= 'Your Continuous Improvement / First Choice team' . "<br><br>";
            $body .= 'Mail: dgf-firstchoice@dhl.com';
        }

        \Aenotification::addUserEmail(
            $this->playid,
            $subject,
            $body,
            $this->model->actionobj->game_id,
            $email
        );

    }

}