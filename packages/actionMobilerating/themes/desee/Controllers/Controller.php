<?php

namespace packages\actionMobilerating\themes\desee\Controllers;

use packages\actionMobilerating\themes\desee\Views\Main;
use packages\actionMobilerating\themes\desee\Views\View as ArticleView;
use packages\actionMobilerating\themes\desee\Models\Model as ArticleModel;

class Controller extends \packages\actionMobilerating\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}
