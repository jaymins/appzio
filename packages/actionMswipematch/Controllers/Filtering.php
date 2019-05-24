<?php

namespace packages\actionMswipematch\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Filtering extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $mobilematchingobj;
    // public $current_playid;
    // public $current_gid;
    public $actionid;
    public $current_id;

    public $data = array();

    public $its_a_match = false;

    public function actionDefault(){

        $this->addFieldSets();

        if($this->getMenuId() == 'reset'){
            $this->model->initMatching();
            $this->mobilematchingobj->resetUnmatches();
        }

        if($this->getMenuId() == 'save'){
            $this->model->saveVariable('recreate_pivot', 1);
            $this->model->saveAllSubmittedVariables();
            $this->model->initMatching();
            $this->model->mobilematchingobj->resetAutoUnmatches();
        }

        return ['Filtering',$this->data];
    }

    private function addFieldSets()
    {
        $num = 1;

        while($num < 5){
            if($this->model->getConfigParam('settings_fields_'.$num)){
                $title = $this->model->getConfigParam('settings_fields_'.$num.'_title');
                $fields = $this->model->getFieldSet($this->model->getConfigParam('settings_fields_'.$num));
                $this->data['fieldsets'][$num] = ['title' => $title,'fields' => $fields];
            }
            $num++;
        }

    }




}