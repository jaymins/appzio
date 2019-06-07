<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMdemoexample\themes\demoexample\Controllers;

use packages\actionMdemoexample\themes\demoexample\Views\View as ArticleView;
use packages\actionMdemoexample\themes\demoexample\Models\Model as ArticleModel;

class Controller extends \packages\actionMdemoexample\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}