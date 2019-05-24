<?php

namespace packages\actionMswipematch\themes\uikit\Controllers;

use packages\actionMswipematch\themes\uiKit\Views\Main;
use packages\actionMswipematch\themes\uiKit\Models\Model as ArticleModel;

class Controller extends \packages\actionMswipematch\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault(){
        $data = array();

        if(strstr($this->getMenuId(),'no_')){
            $id = str_replace('no_','',$this->getMenuId());
            $this->skip($id);
        }

        if(strstr($this->getMenuId(),'yes_')){
            $id = str_replace('yes_','',$this->getMenuId());
            $this->doMatch($id);
        }

        $this->feedModel();

        $search_dist = 500;
        if ($this->model->getSavedVariable('filter_distance')) {
            $search_dist = $this->model->getSavedVariable('filter_distance');
        }

        $this->model->searchTrainers = 1;
        $params['search_distance'] = $search_dist;
        $users = $this->model->getUsersNearby($params);


        $data['users'] = $users;
        if ( $users ) {
            return ['Matching', $data];
        } else {
            return ['Nomatches', $data];
        }

    }

    public function doMatch($id=false){
        if($id){
            $this->model->initMatching($id);
        }

        $this->model->saveMatch();
    }

    public function skip($id=false){
        if($id){
            $this->model->initMatching($id);
        }

        $this->model->skipMatch();
    }

}