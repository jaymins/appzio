<?php

namespace packages\actionMswipematch\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Swiper extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $mobilematchingobj;
    // public $current_playid;
    // public $current_gid;
    public $actionid;

    public $its_a_match = false;

    public function actionDefault(){
        $data = array();

        $this->model->rewriteActionConfigField('transparent_statusbar', 1);
        $this->model->rewriteActionConfigField('hide_menubar', 1);
        $data['icon_color'] = $this->model->getConfigParam('icon_colors') ? $this->model->getConfigParam('icon_colors') : 'black';

        $data = $this->configureTopMenu($data);

        $data['bottom_menu'] = $this->model->getConfigParam('bottom_menu_id');

        if(!$this->model->getSavedVariable('lat') OR !$this->model->getSavedVariable('lat')){
            return ['Collectlocation', $data];
        }

        $this->handleSwipes();

        if($this->its_a_match){
            $data['user'] = $this->model->getUser($this->its_a_match);
            return ['Match',$data];
        }

        $this->model->createPivotView();

        if($this->model->initMatching()){
            $params['extra_selects'] = $this->model->selectForBookmarks();
            $params['extra_joins'] = $this->model->joinForBookmarks();
            $params['distance'] = $this->model->getSavedVariable('filter_distance');

            if(!$this->model->users_nearby){
                $this->model->users_nearby = $this->model->getUsersNearbyV2($params);
            }

            $data['users'] = $this->model->users_nearby;
        }

        if ( $data['users'] ) {
            return ['Matching',$data];
        } else {
            $this->no_output = false;
            $data['mode'] = 'home';
            return ['Nomatches',$data];
        }

    }




}