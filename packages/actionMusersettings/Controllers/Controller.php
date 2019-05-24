<?php

namespace packages\actionMusersettings\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMusersettings\Models\Model as ArticleModel;
use packages\actionMusersettings\Views\View as ArticleView;

class Controller extends BootstrapController
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $data = array();


    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault()
    {

        $data = $this->data;
        $data['fieldlist'] = $this->model->getFieldlist();
        return ['View', $data];
    }


    public function actionSave()
    {
        $this->model->validateSettings();

        if (empty($this->model->validation_errors)) {
            $this->model->saveVariable('recreate_pivot', 1);
            $this->model->saveSettings();
        }

        return $this->actionDefault();
    }

}
