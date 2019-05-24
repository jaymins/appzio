<?php

namespace packages\actionMswipematch\themes\matchswapp\Controllers;

use packages\actionMswipematch\themes\uiKit\Views\Main;
use packages\actionMswipematch\themes\uiKit\Models\Model as ArticleModel;

class Grid extends \packages\actionMswipematch\Controllers\Controller {

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
        $data['users'] = array();

        if($this->its_a_match){
             $data['user'] = $this->model->getUser($this->its_a_match);
            return ['Match',$data];
        }

        $data['icon_color'] = $this->model->getConfigParam('icon_colors') ? $this->model->getConfigParam('icon_colors') : 'black';

        if(!$this->model->getSavedVariable('lat') OR !$this->model->getSavedVariable('lat')){
            return ['Collectlocation', $data];
        }

        $this->feedModel();

        $search_dist = 500;

        if ($this->model->getSavedVariable('filter_distance')) {
            $search_dist = $this->model->getSavedVariable('filter_distance');
        }

        if($this->model->initMatching()){
            $params['extra_selects'] = $this->model->selectForBookmarks();
            $params['extra_joins'] = $this->model->joinForBookmarks();
            $params['distance'] = $search_dist;
            $params['limit'] = 20;
            $data['users'] = $this->model->getUsersNearbyV2($params);
        }

        if ( $data['users'] ) {
            return ['Grid', $data];
        } else {
            return ['Nomatches', $data];
        }
    }





}