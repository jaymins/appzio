<?php

namespace packages\actionMprofile\themes\example\Controllers;

use packages\actionMprofile\themes\example\Views\Main;
use packages\actionMprofile\themes\example\Views\Edit as ArticleView;
use packages\actionMprofile\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMprofile\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}
