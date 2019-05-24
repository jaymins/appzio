<?php

namespace packages\actionMswipematch\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Mymatches extends Controller {

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

        $data['users'] = array();
        $data['icon_color'] = $this->model->getConfigParam('icon_colors') ? $this->model->getConfigParam('icon_colors') : 'black';

        $data = $this->configureTopMenu($data);

        if($this->model->initMatching()){
            $data['matches'] = $this->model->listMyMatches();
/*            $data['i_liked'] = $this->model->getMyMatches();
            print_r($data);die();
            $data['liked_me'] = $this->model->getUsersWhoHaveMatchedMe();
            $data['i_ignored'] = $this->model->getUsersWhoHaveMatchedMe();
            $data['blocked'] = array();*/
        }

        if ( $data['users'] ) {
            return ['Mymatches',$data];
        } else {
            $this->no_output = false;
            return ['Mymatches',$data];
        }


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
                $id = str_replace('left','',$id);
                $this->model->initMatching($id);
                if($this->model->saveMatch() == true){
                    $this->its_a_match = $id;
                }
            }

            // set no output to true not to have server return just an ok for swiping action
            $this->no_output = true;
        }
    }



}