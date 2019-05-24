<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMofficernd\themes\uikit\Controllers;

use packages\actionMofficernd\themes\uikit\Views\View as ArticleView;
use packages\actionMofficernd\themes\uikit\Models\Model as ArticleModel;

class Controller extends \packages\actionMofficernd\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}
