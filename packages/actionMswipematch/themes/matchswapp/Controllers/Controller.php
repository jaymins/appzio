<?php

namespace packages\actionMswipematch\themes\matchswapp\Controllers;

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
        
        $this->model->rewriteActionConfigField('transparent_statusbar', 1);
        $this->model->rewriteActionConfigField('hide_menubar', 1);

        if(!$this->model->getSavedVariable('lat') OR !$this->model->getSavedVariable('lat')){
            return ['Collectlocation', $data];
        }

        $data['icon_color'] = $this->model->getConfigParam('icon_colors') ? $this->model->getConfigParam('icon_colors') : 'black';

        $this->handleSwipes();
        $this->feedModel();


        $search_dist = 500;
        if ($this->model->getSavedVariable('filter_distance')) {
            $search_dist = $this->model->getSavedVariable('filter_distance');
        }

        $this->model->searchTrainers = 1;
        $params['search_distance'] = $search_dist;
        $users = $this->model->getUsersNearbyV2($params);

        $data['users'] = $users;
        if ( $users ) {
            return ['Matching', $data];
        } else {
            return ['Nomatches', $data];
        }

    }


}