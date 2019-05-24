<?php

namespace packages\actionMswipematch\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Showprofile extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $mobilematchingobj;
    // public $current_playid;
    // public $current_gid;
    public $actionid;
    public $current_id;

    public $its_a_match = false;

    public function actionDefault(){
        $data = array();

        if($this->current_id){
            $id = $this->current_id;
        }elseif($this->getMenuId()){
            $id = $this->getMenuId();
            $this->model->sessionSet('current_profile',$id);
        } else {
            $id = $this->model->sessionGet('current_profile');
        }
        $data['interstitials'] = $this->setInterstitials();
        $data['user_info'] = $this->model->getUser($id);

        if(isset($data['user_info']['vars'])){
            $nick = $this->model->getNickname($data['user_info']['vars']);
        } else {
            $nick = '{#anonymous#}';
        }
        
        $this->model->rewriteActionConfigField('subject', $nick);
        $this->model->rewriteActionField('subject', $nick);

        return ['Showprofile',$data];
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

    public function setInterstitials()
    {

        if($this->model->getConfigParam('enable_interstitials')){
            $count = $this->model->getConfigParam('interstitial_threshold',10);
            $counter = $this->model->sessionGet('interstitial_counter');

            if(!$counter){
                $this->model->sessionSet('interstitial_counter', 1);
                return false;
            }

            if($count < $counter){
                $this->model->sessionSet('interstitial_counter', 1);
                return true;
            }

            $this->model->sessionSet('interstitial_counter', $counter+1);
        }

        return false;

    }




}