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

class Viewbooking extends Controller {

    public $view;

    /* @var Model */
    public $model;
    public $data = ['deleted' => false,'error' => false];

    public function actionDefault(){

        if(isset($_REQUEST['error_desription'])){
            $this->data['error'] = $_REQUEST['error_desription'];
        }

        if(is_numeric($this->getMenuId())){
            $id = $this->getMenuId();
            $this->model->sessionSet('booking_id', $id);
        } else {
            $id = $this->model->sessionGet('booking_id');
        }

        $this->data['booking_id'] = $id;
        $this->data['booking'] = $this->model->getBooking($id);

        if($this->getMenuId() == 'timerexpired'){
            if(isset($this->data['booking']['FromTime'])){
                $time = strtotime($this->data['booking']['FromTime']);
            }
            // todo: if the time is very soon, update the view to show door opening etc.
        }

        return ['Viewbooking',$this->data];
    }

    public function actionPreparebooking(){
        $id = $this->getMenuId();
        $this->model->setBookingDataFromExistingBooking($id);
        $this->no_output = true;
        return true;
    }

    public function actionCancel(){
        $id = $this->model->getMenuId();

        if($this->model->deleteBooking($id)){
            $this->data['deleted'] = true;
            return ['Viewbooking',$this->data];
        } else {
            return $this->actionDefault();
        }
    }

    public function actionTimerexpired(){
        return self::actionDefault();
    }



}
