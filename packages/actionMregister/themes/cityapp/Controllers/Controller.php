<?php

namespace packages\actionMregister\themes\cityapp\controllers;

//use packages\actionMregister\themes\cityapp\Views\Main;
use packages\actionMregister\themes\cityapp\Models\Model as ArticleModel;
use packages\actionMregister\themes\cityapp\Views\View as ArticleView;

class Controller extends \packages\actionMregister\Controllers\Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $data = [];
        return ['View', $data];
    }

    public function actionPageOne()
    {
        /* if user has clicked the signuop, we will first validate
        and then save the data. validation errors are also available to views and components. */

        $data = [];

        $this->model->validatePage1();

        if ( isset($this->model->validation_errors['email_exists']) )
            unset( $this->model->validation_errors['email_exists'] );
        
        if (empty($this->model->validation_errors)) {
            $this->model->savePage1();
            $this->model->closeLogin();
            return ['Redirect'];
        }

        return ['View', $data];
    }

}