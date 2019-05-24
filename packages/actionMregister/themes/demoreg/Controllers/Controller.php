<?php

namespace packages\actionMregister\themes\demoreg\controllers;

use packages\actionMregister\themes\demoreg\Models\Model;
use packages\actionMregister\themes\example\Views\Main;
use packages\actionMregister\themes\example\Views\View as ArticleView;
use packages\actionMregister\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMregister\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var Model */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault(){


        /* if user has already completed the first phase, move to phase 2 */
        if($this->model->sessionGet('reg_phase') == 2){
            return $this->actionPagetwo();
        }

        if($this->getMenuId() == 'update_location'){
            $obj = new \stdClass();
            $obj->action = 'ask-location';
            $this->onloads[] = $obj;
        } else {
            $this->collectLocation();
        }

        $this->model->setUserAddress();
        $data['fieldlist'] = $this->model->getFieldlist();
        $data['current_country'] = $this->model->getCountry();
        $data['mode'] = 'show';

        /* if user has clicked the signuop, we will first validate
        and then save the data. validation errors are also available to views and components. */
        if($this->getMenuId() == 'signup'){
            $this->model->validatePage1();

            if(empty($this->model->validation_errors)){
                /* if validation succeeds, we save data to variables and move user to page 2*/
                $this->model->sessionSet('reg_phase', 2);
                $this->model->savePage1();
                return ['Pagetwo',$data];
            }
        }

        return ['View',$data];
    }

    public function actionPagetwo(){

        $data['mode'] = 'show';
        $data['fieldlist'] = $this->model->getFieldlist();

        /* no validation here */
        if($this->getMenuId() == 'done'){
            $this->model->validatePage2();
            if(empty($this->model->validation_errors)) {
                $this->model->savePage2();

                $this->model->closeLogin();
                $data['mode'] = 'close';
                return ['Complete', $data];
            }
        }

        return ['Pagetwo',$data];

    }




}