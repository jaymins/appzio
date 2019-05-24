<?php

namespace packages\actionMusersettings\themes\adidas\Controllers;

use packages\actionMusersettings\themes\adidas\Views\Main;
use packages\actionMusersettings\themes\adidas\Views\View as ArticleView;
use packages\actionMusersettings\themes\adidas\Models\Model as ArticleModel;

class Controller extends \packages\actionMusersettings\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
        $this->model->rewriteActionConfigField('background_color', '#ffffff');
    }

    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){

        $role = $this->model->getSavedVariable('role');
        $data['fieldlist'] = $this->model->getFieldlist();


        return ['View',$data];
    }


    public function actionSave() {

        $data['saved'] = 1;
        $data['fieldlist'] = $this->model->getFieldlist();
        $role = $this->model->getSavedVariable('role');

        $this->model->validateSettings();

        if (empty($this->model->validation_errors)) {
            $this->model->saveSettings();
        }

        return ['View',$data];

    }

}