<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMplug\themes\example\Controllers;

use packages\actionMplug\themes\example\Views\View as ArticleView;
use packages\actionMplug\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionMplug\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}