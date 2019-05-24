<?php

namespace packages\actionMsubscription\themes\example\Controllers;

use packages\actionMsubscription\themes\example\Views\Main;
use packages\actionMsubscription\themes\example\Views\View as ArticleView;
use packages\actionMsubscription\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMsubscription\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }



}
