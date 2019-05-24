<?php

namespace packages\actionMlogin\themes\uiKit\Controllers;
use packages\actionMlogin\themes\uiKit\Models\Model as ArticleModel;

class Controller extends \packages\actionMlogin\Controllers\Controller
{
    public $view;

    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {

        if ($this->getMenuId() == 'do-fb-login') {
            return $this->actionFblogin();
        }

        if($this->getMenuId() == 'after-reset'){
            $this->model->validation_errors['password'] = '{#please_enter_your_new_password#}';
        }

        $data['finishLogin'] = 0;
        $data['errors'] = '';
        return ['Login',  $data];
    }

    public function actionLogin()
    {
        $data = [];
        $data['finishLogin'] = 0;

        $email = strtolower($this->model->getSubmittedVariableByName('email'));
        $password = sha1(strtolower(trim($this->model->getSubmittedVariableByName('password'))));

        if(strlen($password) > 3) {
            $login = $this->model->doLogin($email, $password);

            if($login){
                $this->playid = $login;
                $this->model->loadVariables();
                $this->finishLogin(true,true,__LINE__);

                $data['finishLogin'] = 1;
            } else {
                $this->model->validation_errors['password'] = '{#user_not_found_or_password_wrong#}';
            }
        } else {
            $this->model->validation_errors['password'] = '{#user_not_found_or_password_wrong#}';
        }

        return ['Login', $data];
    }



}