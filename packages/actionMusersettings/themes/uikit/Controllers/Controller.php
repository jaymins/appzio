<?php

namespace packages\actionMusersettings\themes\uikit\Controllers;

use packages\actionMusersettings\themes\uikit\Views\Main;
use packages\actionMusersettings\themes\uikit\Views\View as ArticleView;
use packages\actionMusersettings\themes\uikit\Models\Model as ArticleModel;

class Controller extends \packages\actionMusersettings\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault(){

        if($this->getMenuId() == 'update_birthday'){
            $this->model->saveVariable('birth_month', $this->model->getSubmittedVariableByName('birth_month'));
            $this->model->saveVariable('birth_day', $this->model->getSubmittedVariableByName('birth_day'));
            $this->model->saveVariable('birth_year', $this->model->getSubmittedVariableByName('birth_year'));
            $this->model->loadVariableContent(true);
        }

        $this->addFieldSets();

        if (strpos($this->getMenuId(), 'saveCheckbox-') === 0) {
            $nameList = str_replace('saveCheckbox-', '', $this->getMenuId());
            $this->model->submitvariables[$nameList] = $this->model->getSavedVariable($nameList);
        }

        if (strstr($this->getMenuId(),'lang_')) {
            $code = str_replace('lang_','',$this->getMenuId());
            $this->model->saveVariable('user_language', $code);
        }

        $loader = $this->model->instagramLoadActive();
        
        if($loader OR $this->getMenuId() == 'activateloader'){
            $this->model->rewriteActionConfigField('poll_interval', 1);
        }

        if($this->getMenuId() == 'activateloader'){
            $this->data['show_instagram_load_button'] = false;
            $this->data['show_insta_loader'] = true;
            $this->model->sessionSet('insta_loader', time());
        } else {
            $this->data['show_instagram_load_button'] = $this->model->instagramLoadButtonActive();
            $this->data['show_insta_loader'] = $loader;
        }

        $this->data['profile_progress'] = $this->model->getProfileProgress();
        $this->data['instagram_images'] = $this->model->getInstaImages();

        return parent::actionDefault();
    }

    public function actionSetinstaimages(){
        $this->model->setInstaImages();
        $this->no_output = true;
        return ['view',[]];
    }

    public function actionSetactiveimage(){
        $this->model->sessionSet('active_image', $this->getMenuId());
        $this->no_output = true;
        return ['view',[]];
    }

    public function actionSelectimage(){
        $image = $this->getMenuId();
        $varnum = $this->model->sessionGet('active_image');

        if($varnum === 'default'){
            $this->model->saveVariable('profilepic', $image);
        } else {
            $this->model->saveVariable('profilepic'.$varnum, $image);
        }
        return $this->actionDefault();
    }

    private function addFieldSets()
    {
        $num = 1;

        while($num < 5){
            if($this->model->getConfigParam('settings_fields_'.$num)){
                $title = $this->model->getConfigParam('settings_fields_'.$num.'_title');
                $fields = $this->model->getFieldSet($this->model->getConfigParam('settings_fields_'.$num));
                $this->data['fieldsets'][$num] = ['title' => $title,'fields' => $fields];
            }
            $num++;
        }

    }

}