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

namespace packages\actionMplug\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMplug\Views\View as ArticleView;
use packages\actionMplug\Models\Model as ArticleModel;

class Controller extends BootstrapController {

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
    public function actionDefault(){

        $data = array();

        if($this->getMenuId() == 'connect'){
            $login = $this->model->doLogin();

            if($login){
                $data['server'] = $login['server'];
                $data['api_key'] = $login['api_key'];
                return ['Showapp',$data];
            } else {
                return ['View',$data];
            }
        }

        return ['View',$data];
    }




}