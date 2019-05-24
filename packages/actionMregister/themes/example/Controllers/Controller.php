<?php

namespace packages\actionMregister\themes\example\controllers;

use packages\actionMregister\themes\example\Views\Main;
use packages\actionMregister\themes\example\Views\View as ArticleView;
use packages\actionMregister\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMregister\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }



}