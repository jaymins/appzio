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

class Makebooking extends Controller {

    public $view;

    /* @var Model */
    public $model;
    public $data = array();
    public $booking_data = array();

    public function actionDefault(){

        if(isset($_REQUEST['stripe_id'])){
            $this->model->saveVariable('stripe_card', $_REQUEST['stripe_id']);
        }

        if($this->model->sessionGet('booking_data') AND $this->getMenuId() != 'bottommenu'){
            return self::getScreen2();
        }

        $this->data['locations'] = $this->model->ApiGetSpaceselectorData();
        $this->data['hours'] = $this->model->getHourSelectorData();
        $this->data['minutes'] = $this->model->getMinuteSelectorData();
        $this->data['lengths'] = $this->model->getLengthSelectorData();
        $this->data['booking_data'] = $this->booking_data;

        return ['Makebooking',$this->data];
    }

    public function actionStep2(){

        if(isset($_REQUEST['stripe_id'])){
            $this->model->saveVariable('stripe_card', $_REQUEST['stripe_id']);
        }

        if($this->getMenuId() == 'update_time'){
            $this->model->updateTime();
            return self::getScreen2();
        }

        if($this->getMenuId() == 'update_length'){
            $this->model->updateLength();
            return self::getScreen2();
        }

        if($this->getMenuId() == 'update_date'){
            $this->model->updateDate();
            return self::getScreen2();
        }

        if($this->model->bookingStep1()){
            return self::getScreen2();
        }

        return self::actionDefault();
    }

    public function actionStep3(){
        if(isset($_REQUEST['stripe_id'])){
            $this->model->saveVariable('stripe_card', $_REQUEST['stripe_id']);
        }

        if($this->model->bookingStep2()){
            return self::getScreen3();
        }

        return self::getScreen2();
    }


    public function actionEditbooking(){
        $this->data['booking_data'] = $this->model->sessionGet('booking_data');

        if($this->model->updateBooking()){
            return ['Makebookingedited',$this->data];
        }

        return self::getScreen2();

    }




    public function actionTimechange(){

        $submit = explode('--', $this->getMenuId());
        $time = $submit[0];
        $booth = $submit[1];
        $time = explode(':', $time);
        $hour = $time[0];
        $minute = $time[1];
        $this->booking_data = $this->model->sessionGet('booking_data');
        $this->booking_data['hour'] = $hour;
        $this->booking_data['minute'] = $minute;
        $this->booking_data['booth'] = $booth;
        $this->model->sessionSet('booking_data', $this->booking_data);
        return self::getScreen2();
    }

    public function getScreen2(){

        $this->data['locations'] = $this->model->ApiGetSpaceselectorData();
        $this->data['booking_data'] = $this->model->sessionGet('booking_data');
        $this->data['booths'] = $this->model->getBooths();
        $this->data['hours'] = $this->model->getHourSelectorData();
        $this->data['minutes'] = $this->model->getMinuteSelectorData();
        $this->data['lengths'] = $this->model->getLengthSelectorData();
        return ['Makebooking2',$this->data];
    }

    public function getScreen3(){

        $this->data['booking_data'] = $this->model->sessionGet('booking_data');
        return ['Makebooking3',$this->data];
    }

    public function actionReset(){


        if($this->getMenuId() == '1'){
            $this->booking_data = $this->model->sessionGet('booking_data');
            $this->model->sessionUnset('booking_data');
            $this->model->sessionUnset('booth_data');
            return self::actionDefault();
        }
    }



}
