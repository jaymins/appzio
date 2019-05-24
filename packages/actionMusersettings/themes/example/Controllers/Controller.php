<?php

namespace packages\actionMusersettings\themes\example\Controllers;

use packages\actionMusersettings\themes\example\Views\Main;
use packages\actionMusersettings\themes\example\Views\View as ArticleView;
use packages\actionMusersettings\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMusersettings\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }



}
