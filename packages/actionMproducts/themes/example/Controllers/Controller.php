<?php

namespace packages\actionMproducts\themes\example\Controllers;

use packages\actionMproducts\themes\example\Views\Main;
use packages\actionMproducts\themes\example\Views\View as ArticleView;
use packages\actionMproducts\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMproducts\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }



}
