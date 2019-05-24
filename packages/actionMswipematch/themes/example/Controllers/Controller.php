<?php

namespace packages\actionMswipematch\themes\example\Controllers;

use packages\actionMswipematch\themes\example\Views\Main;
use packages\actionMswipematch\themes\example\Views\View as ArticleView;
use packages\actionMswipematch\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMswipematch\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }



}