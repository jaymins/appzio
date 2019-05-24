<?php

use packages\actionMnexudus\Models\Model;

/**
 * This example shows a simple registration form. Usually this action would be used in conjuction with
 * Mobilelogin action, which provides login and logout functionalities.
 *
 * Default controller for your action. If no other route is defined, the action will default to this controller
 * and its default method actionDefault() which must always be defined.
 *
 * In more complex actions, you would include different controller for different modes or phases. Organizing
 * the code for different controllers will help you keep the code more organized and easier to understand and
 * reuse.
 *
 * Unless controller has $this->no_output set to true, it should always return the view file name. Data from
 * the controller to view is passed as a second part of the return array.
 *
 * Theme's controller extends this file, so usually you would define the functions as public so that they can
 * be overriden by the theme controller.
 *
 */

namespace packages\actionMnexudus\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMnexudus\Views\View as ArticleView;
use packages\actionMnexudus\Models\Model as ArticleModel;

class Verification extends Controller
{

    public $view;

    /* @var Model */
    public $model;
    public $data = array('can_skip' => false, 'send_verification' => false, 'check_doorflow' => false);

    public function actionDefault()
    {

        if(!$this->model->getSubmittedVariableByName('authenticate_status') AND $this->model->getSubmittedVariableByName('authenticate_description')){
            $this->model->deleteVariable('phone_verified');
            $this->model->saveVariable('verification_sent', 1);
            return ['OpenDoor\Phoneentercode',$this->data];
        }

        $this->data['can_skip'] = true;
        $this->model->setUserInfo();
        return ['OpenDoor\Phone', $this->data];
    }

    public function actionSkipverify()
    {
        $this->model->saveVariable('phone_verified', 2);
        $this->model->deleteVariable('verification_sent');
        $this->model->deleteVariable('doorflow_not_authenticated');
        return $this->home();
    }

    // this gets called after login verification in case the OpenDoor SDK is no longer authenticated
    public function actionVerificate(){

        $error = $this->model->getSubmittedVariableByName('authenticate_error');
        $status = $this->model->getSubmittedVariableByName('authenticate_status');
        $description = $this->model->getSubmittedVariableByName('authenticate_description');

        if(!$error){
            return $this->verificationCode();
        }

        return $this->phoneNumber();
    }

    private function phoneNumber(){
        $this->model->deleteVariable('phone_verified');
        $this->model->bottom_menu_id = 0;
        $this->data['can_skip'] = true;
        return ['OpenDoor\Phone',$this->data];
    }

    private function home(){
        $this->model->setUserInfo();
        $this->data['booking'] = $this->model->getMyNextBooking();
        return ['Home',$this->data];
    }

    private function verificationCode(){
        $this->model->deleteVariable('phone_verified');
        $this->model->saveVariable('verification_sent', 1);
        $this->model->bottom_menu_id = 0;
        $this->data['can_skip'] = true;
        return ['OpenDoor\Phoneentercode',$this->data];
    }




    public function actionEntercode()
    {
        if(!$this->model->getSubmittedVariableByName('authenticate_status') AND $this->model->getSubmittedVariableByName('authenticate_description')){
            $this->model->deleteVariable('phone_verified');
            $this->model->saveVariable('verification_sent', 1);
            return ['OpenDoor\Phoneentercode',$this->data];
        }

        $this->model->deleteVariable('phone_verified');
        $this->model->bottom_menu_id = 0;
        $this->data['can_skip'] = true;
        return ['OpenDoor\Phone', $this->data];

    }

    public function actionVerificationdone()
    {

        $error = $this->model->getSubmittedVariableByName('authenticate_error');
        $status = $this->model->getSubmittedVariableByName('authenticate_status');
        $description = $this->model->getSubmittedVariableByName('authenticate_description');

        if($error){
            $this->model->validation_errors['code'] = $error;
            $this->model->bottom_menu_id = 0;
            $this->data['can_skip'] = true;
            return ['OpenDoor\Phoneentercode',$this->data];
        }

        $this->model->deleteVariable('verification_sent');

        if($this->model->sessionGet('after_verify') == 'home'){
            return $this->home();
        }

        return ['OpenDoor\Phoneverifydone',$this->data];

    }

    public function actionActivateverify()
    {
        $this->model->deleteVariable('phone_verified');
        $this->model->bottom_menu_id = 0;
        $this->data['can_skip'] = true;
        return ['OpenDoor\Phone', $this->data];
    }

    public function actionVerifyphone()
    {

        if ($this->getMenuId() != 'doverify') {
            $this->data['send_verification'] = false;
            return ['OpenDoor\Phoneentercode', $this->data];
        }

        if ($this->model->savePhone()) {
            $this->data['send_verification'] = true;
            $this->model->sessionSet('after_verify', 'confirmation');
            return ['OpenDoor\Phonecheck', $this->data];
        } else {
            return ['OpenDoor\Phone', $this->data];
        }

    }


    public function actionVerifyphoneerror()
    {
        $this->data['can_skip'] = true;
        $this->model->validation_errors['phone'] = '{#unfortunately_there_was_an_error_in_verifying_your_phone#}';
        return ['Phone', $this->data];


    }


}
