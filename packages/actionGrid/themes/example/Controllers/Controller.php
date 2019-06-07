<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionGrid\themes\example\Controllers;

use packages\actionGrid\themes\example\Views\View as ArticleView;
use packages\actionGrid\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionGrid\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}