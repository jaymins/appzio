<?php

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

namespace packages\actionMcalendar\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMcalendar\Models\Model as ArticleModel;
use packages\actionMcalendar\Views\View as ArticleView;

class Weekly extends BootstrapController
{

    /**
     * @var ArticleView
     */
    public $view;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;
    public $data = array();

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault()
    {

        date_default_timezone_set('UTC');

        $this->noSwipe();
        $this->data['active_date'] = $this->model->getActiveDateWeek();
        $this->data['calendar'] = $this->model->getWeek();
        $this->data['active_timestamp'] = $this->model->active_date_week ? $this->model->active_date_week : time();
        return ['Weekly', $this->data];
    }

    public function noSwipe(){
        if($this->model->getSavedVariable('swipe_off')){
            $this->data['noswipe'] = true;
        } else {
            $this->data['noswipe'] = false;
        }
    }


    public function actionUpdatecalendarweek()
    {

        if (isset($this->model->submitvariables['calendar_weekly'])) {
            $this->model->active_date_week = $this->model->submitvariables['calendar_weekly'];
        }

        return $this->actionDefault();
    }

    public function actionRearrange()
    {
        $this->no_output = true;
        return ['View', $this->data];
    }

}