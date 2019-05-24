<?php

namespace packages\actionMobilerating\themes\uikit\Controllers;

use packages\actionMobilerating\themes\uikit\Views\Main;
use packages\actionMobilerating\themes\uikit\Views\View as ArticleView;
use packages\actionMobilerating\themes\uikit\Models\Model as ArticleModel;

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
