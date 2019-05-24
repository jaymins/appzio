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

namespace packages\actionMgdpr\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMgdpr\Views\View as ArticleView;
use packages\actionMgdpr\Models\Model as ArticleModel;

class Controller extends BootstrapController
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

    public $data = ['delete' => false, 'home' => 0,'popup' => false,'header' => '','tabs' => []];

    public function actionDefault()
    {

        $this->setOptions();
        $this->setTabs();
        return ['View', $this->data];
    }


    public function actionRemoveuser()
    {
        $data = array();
        $this->model->deleteUser();
        $this->no_output = true;
        return ['View', $data];
    }

    private function setOptions()
    {
        if ($this->model->getSavedVariable('logged_in') == 1
            AND $this->model->getConfigParam('show_delete')) {
            $this->data['delete'] = true;
        }

        if ($this->model->getSavedVariable('logged_in') == 1
            AND $this->model->getConfigParam('home_action_logged_in')){
            $this->data['home'] = $this->model->getConfigParam('home_action_logged_in');
        } elseif($this->model->getConfigParam('home_action')){
            $this->data['home'] = $this->model->getConfigParam('home_action');
        }

        if($this->getMenuId() == 'popup'){
            $this->data['popup'] = true;
            $this->model->sessionSet('gdpr_popup', 1);
        } elseif($this->model->getConfigParam('popup')){
            $this->data['popup'] = true;
        } elseif($this->model->sessionGet('gdpr_popup')){
            $this->data['popup'] = true;
        }

        if($this->getMenuId() == 'no-popup') {
            $this->model->sessionUnset('gdpr_popup');
        }

    }

    private function setTabs()
    {
        if($this->model->getConfigParam('terms')){
            $this->data['tabs']['terms'] = $this->model->getConfigParam('terms');

            if($this->model->getConfigParam('headertext')){
                $this->data['headertext'] = $this->model->getConfigParam('headertext');
            }

            if($this->model->getConfigParam('privacy')){
                $this->data['tabs']['privacy'] = $this->model->getConfigParam('privacy');
            }

            if($this->model->getConfigParam('subscriptions_ios')){
                $this->data['tabs']['subscriptions_ios'] = $this->model->getConfigParam('subscriptions_ios');
            }

            if($this->model->getConfigParam('subscriptions_android')){
                $this->data['tabs']['subscriptions_android'] = $this->model->getConfigParam('subscriptions_android');
            }
        } else {
            // this is basically backwards compatibility mode
            $this->data['tabs']['terms'] = $this->model->getConfigParam('headertext');
        }

    }


}
