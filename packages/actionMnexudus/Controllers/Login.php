<?php



namespace packages\actionMnexudus\Controllers;
use packages\actionMnexudus\Models\Model;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMnexudus\Views\View as ArticleView;
use packages\actionMnexudus\Models\Model as ArticleModel;

class Login extends Controller {

    public $view;

    /* @var Model */
    public $model;
    public $data = ['password_reset' => false];

    public function actionDefault(){
        if($this->getMenuId() == 'dologin'){
            if($this->model->loginUser()){
                return ['Complete',$this->data];
            }
        }

        return ['Login',$this->data];
    }

    public function actionResetpass(){

        if($this->model->getSubmittedVariableByName('email') AND $this->getMenuId() == 'reset'){
            $email = $this->model->getSubmittedVariableByName('email');

            if($this->model->validateEmail($email)){
                $this->model->resetPassword($this->model->getSubmittedVariableByName('email'));
                $this->data['password_reset'] = true;
            } else {
                $this->model->validation_errors['email'] = '{#please_check_your_email_address#}';
            }
        }

        return self::actionDefault();
    }




}
