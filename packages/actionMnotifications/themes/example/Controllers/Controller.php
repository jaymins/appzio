<?php

namespace packages\actionMnotifications\themes\example\Controllers;

use packages\actionMnotifications\themes\example\Views\Main;
use packages\actionMnotifications\themes\example\Views\View as ArticleView;
use packages\actionMnotifications\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMnotifications\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }



}
