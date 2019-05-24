<?php

namespace packages\actionMtasks\themes\example\Controllers;

use packages\actionMtasks\themes\example\Views\Main;
use packages\actionMtasks\themes\example\Views\View as ArticleView;
use packages\actionMtasks\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMtasks\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }



}
