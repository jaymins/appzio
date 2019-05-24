<?php

namespace packages\actionMnotifications\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\themes\ProficientTraderIntro\Models\AenotificationModel;
use packages\actionMnotifications\Views\View as ArticleView;
use packages\actionMnotifications\Models\Model as ArticleModel;

class Controller extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;


    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){

        $data = array();

        if(stristr($this->getMenuId(),'mark_read_')){
            $id = str_replace('mark_read_','',$this->getMenuId());
            $this->model->markRead($id);
        }

        if(stristr($this->getMenuId(),'delete_')){
            $id = str_replace('delete_','',$this->getMenuId());
            $this->model->deleteNotification($id);
        }

        if($this->model->getMarkReadMenu()){
            $this->model->markAllRead();
        }

        $this->model->configureMenu();
        
        $data['notifications'] = $this->model->getMyNotifications();
        return ['View',$data];
    }


    public function actionPagetwo(){

        $data['mode'] = 'show';

        /* no validation here */
        if($this->getMenuId() == 'done'){
            $this->model->closeLogin();
            $data['mode'] = 'close';
        }

        return ['Pagetwo',$data];

    }

    public function actionDelete(){
        $id = $this->getMenuId();
        $this->model->notifications->deleteByPk($id);
        $this->no_output = true;
        return ['View',[]];

    }

}