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

class Home extends Controller {

    public $view;

    /* @var Model */
    public $model;
    public $data = array('can_skip' => false,'send_verification' => false,'check_doorflow' => false);

    public function actionDefault(){

        $this->model->setUserInfo();

        if(!$this->model->getSavedVariable('opendoor_phone')){
            $this->model->bottom_menu_id = 0;
            $this->data['can_skip'] = true;
            return ['OpenDoor\Phone',$this->data];
        }

        // if user has just logged in and and doorflow has been verified
        // we will try to call the verify
        if($this->model->getSavedVariable('doorflow_not_authenticated') AND $this->model->getSavedVariable('opendoor_phone')){
            $this->model->sessionSet('after_verify', 'home');
            return ['OpenDoor\Phonecheck',$this->data];
        }

        if($this->model->getSavedVariable('verification_sent')){
            $this->model->bottom_menu_id = 0;
            $this->data['can_skip'] = true;
            return ['OpenDoor\Phoneentercode',$this->data];
        }

        $this->data['booking'] = $this->model->getMyNextBooking();
        return ['Home',$this->data];
    }

    public function actionVerificationdone(){
        $this->model->sessionUnset('after_verify');
        $this->model->saveVariable('phone_verified', 1);
        return $this->actionDefault();
    }

    public function actionVerificationcode(){

    }

    public function actionActivateverify(){

        $this->model->deleteVariable('phone_verified');
        $this->model->bottom_menu_id = 0;
        $this->data['can_skip'] = true;
        return ['OpenDoor\Phone',$this->data];
    }

    public function actionVerifyphone(){

        if(isset($_REQUEST['error_description'])){
            $this->model->validation_errors['code'] = $_REQUEST['error_description'];
        }

        if($this->getMenuId() != 'doverify'){
            $this->data['send_verification'] = false;
            return ['OpenDoor\Phoneentercode',$this->data];
        }

        if($this->model->savePhone()){
            $this->data['send_verification'] = true;
            return ['OpenDoor\Phoneentercode',$this->data];
        } else {
            return ['OpenDoor\Phone',$this->data];
        }

    }

    public function actionSkipverify(){
        $this->model->saveVariable('phone_verified', 2);
        $this->model->deleteVariable('verification_sent');
        return $this->actionDefault();
    }

    public function actionVerifyphoneerror(){
        $this->data['can_skip'] = true;
        $this->model->validation_errors['phone'] = '{#unfortunately_there_was_an_error_in_verifying_your_phone#}';
        return ['Phone',$this->data];


    }



}
