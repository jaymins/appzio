<?php

namespace packages\actionMswipematch\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Infinite extends Controller {

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
        $data['logoimage'] = '';
        $data['menu'] = [];

        $data['notification_count'] = $this->model->notifications->getMyNotificationCount($this->playid);
        $data['icon_color'] = $this->model->getConfigParam('icon_colors');

        if(!$this->model->getSavedVariable('lat') OR !$this->model->getSavedVariable('lon')){
            $data['collect_location'] = $this->getTimedBool();
        }

        $data['original_image_dimensions'] = false;

        if($this->model->getConfigParam('original_image_dimensions')){
            $data['original_image_dimensions'] = true;
        }

        if($this->model->sessionGet('checked_in')){
            $data['checked_in'] = $this->model->getSavedVariable('current_venue');
            $this->model->sessionSet('checked_in',false);
        }

        $data = $this->configureTopMenu($data);

        $this->handleSwipes();

        if($this->its_a_match){
            $data['user'] = $this->model->getUser($this->its_a_match);
            return ['Match',$data];
        }

        $this->model->createPivotView();


        if($this->model->initMatching()){
            $data['users'] = $this->getUsers();
        }

        if ( $data['users'] ) {
            return ['Infinite',$data];
        } else {
            $this->no_output = false;
            return ['Nomatches',$data];
        }

    }

    public function getUsers(){

        $params['extra_selects'] = $this->model->selectForBookmarks();
        $params['extra_joins'] = $this->model->joinForBookmarks();
        $params['distance'] = $this->model->getSavedVariable('filter_distance');

        if(isset($_REQUEST['next_page_id'])){
            $params['limit'] = $_REQUEST['next_page_id']*15 .',15';
            $this->model->users_nearby = $this->model->getUsersNearbyV2($params);
        } else {
            $params['limit'] = '0,50';

            if(!$this->model->users_nearby){
                $this->model->users_nearby = $this->model->getUsersNearbyV2($params);
            }
        }

        if(is_array($this->model->users_nearby)){
            $this->model->users_nearby = array_slice($this->model->users_nearby,0,15);
        }

        return $this->model->users_nearby;
    }

    public function actionUnlike(){
        $id = $this->getMenuId();
        $this->model->initMatching($id);
        $this->model->skipMatch();
        $this->no_output = true;
        return ['Nomatches',[]];
    }

    public function actionLike(){
        $id = $this->getMenuId();
        $this->model->initMatching($id);
        $match = $this->model->saveMatch();
        if($match){
            $this->its_a_match = $id;
            $this->feedModel($id);
            $data['user'] = $this->model->getUser($this->its_a_match);
            return ['Match',$data];
        }

        $this->no_output = true;
        return ['Nomatches',[]];
    }





}