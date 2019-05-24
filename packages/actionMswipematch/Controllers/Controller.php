<?php

namespace packages\actionMswipematch\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Controller extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $mobilematchingobj;

    public $actionid;
    public $its_a_match;

    public $current_id;

    public function actionDefault(){
        $data = array();

        $this->feedModel();
        $this->handleSwipes();
        $data['icon_color'] = $this->model->getConfigParam('icon_colors') ? $this->model->getConfigParam('icon_colors') : 'black';

        if($this->its_a_match){
            $data['user'] = $this->model->getUser($this->its_a_match);
            return ['Match',$data];
        }

        $params['search_distance'] = $this->model->getSavedVariable('filter_distance') ? $this->model->getSavedVariable('filter_distance') : 100000;

        if(!$this->model->users_nearby){
            $this->model->users_nearby = $this->model->getUsersNearbyV2($params);
        }

        $data['users'] = $this->model->users_nearby;

        if ( $this->model->users_nearby ) {
            return ['Matching',$data];
        } else {
            return ['Nomatches',$data];
        }
    }

    public function configureTopMenu($data){
        /* top menu configuration */
        if($this->model->getConfigParam('actionimage2')){
            $this->model->rewriteActionConfigField('hide_menubar', 1);
            $data['logoimage'] = $this->model->getConfigParam('actionimage2');
        }

        $menu = $this->model->getConfigParam('menu_id');

        if($menu){
            $this->model->rewriteActionConfigField('hide_menubar', 1);
            $this->model->rewriteActionConfigField('backarrow', 0);
            $menu = $this->model->getMenuData($menu);
            if(isset($menu->action) AND isset($menu->action_config) AND isset($menu->icon)) {
                $data['menu'] = ['action' => $menu->action, 'config' => $menu->action_config, 'icon' => $menu->icon];
            }
        }

        return $data;
    }

    public function feedModel($otheruserid=false){
        $this->model->playid_thisuser = $this->playid;
        $this->model->playid_otheruser = $otheruserid;
        $this->model->initMatching($otheruserid,true);
    }

    public function actionPagetwo(){
        $data['mode'] = 'show';
        return ['Pagetwo',$data];
    }

    public function actionBookmark($id=false){
        if(!$id){
            $id = $this->getMenuId();
        }

        if($id){
            $this->model->addBoomark($id);
        }

        $this->no_output = true;
        return ['View',[]];

    }

    public function actionRemovebookmark($id=false){
        if(!$id){
            $id = $this->getMenuId();
        }

        if($id){
            $this->model->removeBoomark($id);
        }

        $this->no_output = true;
        return ['View',[]];

    }

    public function actionLike(){
        $this->model->initMatching($this->getMenuId());
        $match = $this->model->saveMatch();

        if($match){
            $this->its_a_match = $this->getMenuId();
        }

        return $this->actionDefault();
    }

    public function actionUnlike(){
        $this->model->initMatching($this->getMenuId());
        $this->model->skipMatch(true);
        return $this->actionDefault();
    }

    public function actionRecordinstaclick(){
        $id = $this->getMenuId();
        if($id){
            $this->model->recordInstaClick($id);
        }
        $this->no_output = true;
        return ['View',[]];


    }

    public function actionBlock(){
        $id = $this->getMenuId();
        if($id){
            $this->model->initMatching($id);
            $this->model->blockUser();
        }
        $this->no_output = true;
        return ['View',[]];

    }

    public function actionReport(){
        $id = $this->getMenuId();

        if($id){
            $this->model->initMatching($id);
            $this->model->reportUser();
        }
        $this->no_output = true;
        return ['View',[]];

    }

    public function handleSwipes(){
        if(isset($_REQUEST['swid'])){
            $id = $_REQUEST['swid'];

            if(strstr($id,'left')){
                $id = str_replace('left','',$id);
                $this->model->initMatching($id);
                $this->model->skipMatch();
            }

            if(strstr($id,'right')){
                $id = str_replace('right','',$id);
                $this->model->initMatching($id);
                $match = $this->model->saveMatch();
                if($match){
                    $this->its_a_match = $id;
                    return true;
                }
            }

            // set no output to true not to have server return just an ok for swiping action
            $this->no_output = true;
        }
        return ['View',[]];

    }


}