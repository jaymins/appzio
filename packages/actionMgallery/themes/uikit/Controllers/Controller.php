<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMgallery\themes\uikit\Controllers;

use packages\actionMgallery\themes\uikit\Views\View as ArticleView;
use packages\actionMgallery\themes\uikit\Models\Model as ArticleModel;

class Controller extends \packages\actionMgallery\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}