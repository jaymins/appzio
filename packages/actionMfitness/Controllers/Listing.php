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

class Listing extends BootstrapController
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

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     * @return array
     */
    private $calendar_id;

    public function actionDefault()
    {
        $data = [];

        $data['sorting'] = $this->model->sessionGet('fitness_sorting');
        $data['exercise_data'] = $this->model->getExerciseData(8,$data['sorting'],true);
        $data['offset'] = isset($_REQUEST['next_page_id']) ? $_REQUEST['next_page_id'] + 15 : 15;
        $data['search_active'] = $this->model->search_term ? true : false;
        return ['Listing', $data];
    }

    public function actionCancelsearch(){
        $this->model->search_term = false;
        return $this->actionDefault();
    }

    public function actionSearch(){
        $this->model->search_term = $this->model->getSubmittedVariableByName('fitness_keyword');
        return $this->actionDefault();
    }

    public function actionAdd()
    {
        $exercise_id = $this->getExerciseId();
        $this->no_output = true;

        if (!$exercise_id) {
            return false;
        }
        $date = $this->model->getSubmittedVariableByName('training_start_date', time());
        $hour = $this->model->getSubmittedVariableByName('training_time-hour', '00');
        $minute = $this->model->getSubmittedVariableByName('training_time-minute', '00');
        $date = strtotime(date('Y-m-d', $date) . ' ' . $hour . ':' . $minute);

        $this->model->addToCalendar($exercise_id,$date);
        return true;
    }
    private function getExerciseId()
    {
        $id = explode('exercise_', $this->getMenuId());

        if (isset($id[1])) {
            return $id[1];
        } else {
            return false;
        }
    }

    /* apply sorting from the div */
    public function actionSorting()
    {
        $sorting = $this->model->getSubmittedVariableByName('sorting');
        $this->model->sessionSet('fitness_sorting',$sorting);
        return $this->actionDefault();
    }

    /* apply key ingredients filter */
    private function getSubmitedList($list, $var)
    {
        $result = [];
        foreach ($list as $key => $item) {
            $key = explode('-', $key, 2);
            if ($item != '' && $key[0] == $var) {
                $result[] = $item;
            };
        }
        return $result;
    }

}