<?php

namespace packages\actionMregister\themes\stepbystep\controllers;

use function is_string;
use packages\actionMregister\themes\stepbystep\Views\View as ArticleView;
use packages\actionMregister\themes\stepbystep\Models\Model as ArticleModel;

class Controller extends \packages\actionMregister\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault(){
        $this->model->rewriteActionConfigField('pull_to_refresh', 1);

        /* if profile picture is not set, we set the default */
/*        if($this->getMenuId() == 'phone' AND !$this->model->getSavedVariable('profilepic')){
            $this->model->saveVariable('profilepic', 'mreg-persona-icon.png');
        }*/

        $phase = $this->model->getPhase();
        $function = 'phase'.$phase;

        return $this->$function();
    }

    /* aka emaroleil */
/*    private function phase1(){
        $data = array();

        if($this->getMenuId() == 'parent' OR $this->getMenuId() == 'child'){
            $this->model->saveVariable('role', $this->getMenuId());
            return ['Email',$data];
            
        }

        return ['Role',$data];
    }*/

    /* aka name */
    private function phase1(){
        $data = array();
        //$this->collectLocation();

        if($this->getMenuId() == 'name'){
            $this->model->validateName();
            if(!$this->model->validation_errors){
                $this->model->getCountryCode();
                return ['Email',$data];
            }
        }

        return ['Name',$data];
    }

    /* aka email */
    private function phase2(){
        $data = array();

        if($this->getMenuId() == 'email'){
            $this->model->validateMyEmail();

            if(!$this->model->validation_errors){
                $this->model->rewriteActionConfigField('pull_to_refresh', 0);
                return ['Birthdate',$data];
            }
        }

        return ['Email',$data];
    }

    /* aka birthday */
    private function phase3(){
        $data = array();
        //$this->model->rewriteActionConfigField('pull_to_refresh', 0);

        if($this->getMenuId() == 'birthdate'){
            $this->model->saveBirthday();
            //$data['country_code'] = $this->model->getCountryCode();
            return ['Password',$data];
        }

        return ['Birthdate',$data];
    }

    /* aka phone */
/*    private function phase4(){
        $data = array();

        $data['country_code'] = $this->model->getMyCountryCode();

        if($this->getMenuId() == 'phone'){
            $this->model->validatePhone();

            if(!$this->model->validation_errors) {
                return ['Password',$data];
            }

            return ['Phone',$data];
        } else {
            $this->collectLocation();
        }

        return ['Phone',$data];
    }*/

    /* aka password */
    private function phase4(){
        $data = array();

        if($this->getMenuId() == 'password'){
            $this->model->validateMyPassword();

            if(!$this->model->validation_errors){
                return ['Username',$data];
            }
        }

        return ['Password',$data];
    }

    /* aka username */
    private function phase5(){
        $data = array();

        if($this->getMenuId() == 'username'){
            $this->model->validateUsername();

            if(!$this->model->validation_errors){
                return ['Photo',$data];
            }
        }

        return ['Username',$data];
    }


    /* aka profile picture */
    private function phase6(){
        $data = array();
        $data['country_code'] = $this->model->getMyCountryCode();

        if($this->getMenuId() == 'photo'){
            // to prefetch the data
            $this->model->closeLogin(true);
            $data['text1'] = '{#all_set#}!';
            $data['text2'] = '{#logging_you_in#}';
            return ['Complete',$data];
        }

        return ['Photo',$data];
    }



    /* aka invite adult */
    private function phase7(){
        $data = array();

        $this->model->closeLogin(true);
        $this->model->transferData();

        $data['text1'] = '{#all_set#}!';
        $data['text2'] = '{#logging_you_in#}';

        return ['Complete',$data];

        if($this->getMenuId() == 'inviteadult'){
            $this->model->validateAdultEmail();
            if(!$this->model->validation_errors){
                return ['Confirmationcode',$data];
            }
        }

        return ['Inviteadult',$data];
    }

    /* aka confirmation code */
    private function phase8(){
        $data = array();

        if($this->getMenuId() == 'createnew'){
            $this->model->cleanRegdata();

            if(!$this->model->validation_errors){
                $data['text1'] = '{#hold_tight#}!';
                $data['text2'] = '{#creating_a_new_user#}';
                return ['Complete',$data];
            }
        }


        return ['Createnew',$data];
    }



}