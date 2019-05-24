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

namespace packages\actionMnexudus\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMnexudus\Models\Model;
use packages\actionMnexudus\Views\View as ArticleView;
use packages\actionMnexudus\Models\Model as ArticleModel;

class Register extends Controller {

    public $view;

    /* @var Model */
    public $model;
    public $data = array();

    public function actionDefault(){

        $this->data['mode'] = 'complete';

        if($this->getMenuId() == 'finish'){
            $this->model->rewriteActionConfigField('background_color', '#353536');
            return ['Complete',$this->data];
        }

        if($this->model->getSavedVariable('password')){
            return ['Intro',$this->data];
        }

        if($this->getMenuId() == 'register'){
            if($this->model->validateRegistration()){
                $this->model->rewriteActionConfigField('background_color', '#353536');
                return ['Intro',$this->data];
            }
        }


        return ['Register',$this->data];
    }



}
