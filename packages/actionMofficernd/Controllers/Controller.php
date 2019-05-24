<?php


namespace packages\actionMofficernd\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMofficernd\Views\View as ArticleView;
//use packages\actionMofficernd\Models\Model as ArticleModel;

class Controller extends BootstrapController {

    /**
     * @var ArticleView
     */
    public $view;

    /**
     * @var ArticleModel
     */
    public $model;

    public $data = array();

    public function actionDefault(){
        return ['View',$this->data];
    }
}
