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

namespace packages\actionMfitness\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMfitness\Models\Model as ArticleModel;
use packages\actionMfitness\Views\View as ArticleView;

class Statistics extends BootstrapController
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

        $this->model->rewriteActionConfigField('background_color', '#000000');

        if($this->model->sessionGet('statistics_date')){
            $date = $this->model->sessionGet('statistics_date');
            $this->data['month'] = $date['month_name'];
            $this->data['month_value'] = $date['month_value'];
        } else {
            $this->data['month'] = date('F');
            $this->data['month_value'] = time();
        }

        $this->data['pr'] = $this->model->getPr();
        $this->data['leaderboard'] = $this->model->getLeaderboard();
        $this->data['leaderboard_filters'] = $this->model->getLeaderboardFilters();

        $this->data['my_points'] = $this->model->getMyPoints();
        $this->data['month_points'] = $this->model->getMonthlyPoints();
        $this->data['my_points_text'] = $this->model->getMyPointsText();
        $this->data['chart'] = $this->model->getMyChart();

        return ['Statistics', $this->data];
    }

    /* set the time period for the chart */
    public function actionSetchart(){
        $this->model->sessionSet('statistics_time', $this->model->getSubmittedVariableByName('statistics_time'));
        return self::actionDefault();
    }

    /* clear filtering for leaderboard */
    public function actionLeaderboardfilterclear(){
        $this->model->clearLeaderBoardFiltering();
        return self::actionDefault();
    }

    /* set leaderboard filtering */
    public function actionLeaderboardfiltering(){
        $this->model->setLeaderBoardFiltering();
        return self::actionDefault();
    }

    public function actionSwitchunit(){
        $this->model->swithcUnits();
        return self::actionDefault();
    }

    /* set the active month */
    public function actionChangemonth(){
        $direction = $this->getMenuId();

        if($this->model->sessionGet('statistics_date')){
            $date = $this->model->sessionGet('statistics_date');
            $time = $date['month_value'];
        } else {
            $time = time();
        }

        if($direction == 'next'){
            $new_time = $newDate = strtotime('+1 month',$time);
        } else {
            $new_time = $newDate = strtotime('-1 month',$time);
        }

        $values['month_name'] = date('F',$new_time);
        $values['month_value'] = $new_time;

        $this->model->sessionSet('statistics_date', $values);
        return self::actionDefault();
    }

    /* save individual PR value. Note, this will always create a new record. */
    public function actionSavepr(){
        $id = $this->getMenuId();
        $value = $this->model->getSubmittedVariableByName($id);
        $this->model->savePr($id,$value);
        $this->no_output = true;
        return ['View',[]];
    }

}