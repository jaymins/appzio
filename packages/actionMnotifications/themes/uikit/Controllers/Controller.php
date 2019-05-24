<?php

namespace packages\actionMnotifications\themes\uikit\Controllers;

use packages\actionMnotifications\themes\uikit\Views\Main;
use packages\actionMnotifications\themes\uikit\Views\View as ArticleView;
use packages\actionMnotifications\themes\uikit\Models\Model as ArticleModel;

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
