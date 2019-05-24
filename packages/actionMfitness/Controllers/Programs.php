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

class Programs extends BootstrapController
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

        $this->data['programs'] = $this->model->getProgramsByCategory();
        $this->data['category_data'] = $this->model->getCategoryData();
        $this->data['search_active'] = $this->model->getSavedVariable('tmp_program_filter') ? true : false;
        return ['Programs', $this->data];
    }

    public function actionSorting()
    {
        $sorting = $this->model->getSubmittedVariableByName('sorting');
        $this->model->saveVariable('tmp_program_sorting', $sorting);
        return $this->actionDefault();
    }

    public function actionSavefilters(){
        $this->model->saveVariable('tmp_program_filter',
            $this->model->getSubmittedVariableByName('keyword'));

        return self::actionDefault();
    }

    public function actionResetfilters(){
        $this->model->deleteVariable('tmp_program_filter');
        return self::actionDefault();
    }

}