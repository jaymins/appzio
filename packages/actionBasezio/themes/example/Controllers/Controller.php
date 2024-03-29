<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionBasezio\themes\example\Controllers;

use packages\actionBasezio\themes\example\Views\View as ArticleView;
use packages\actionBasezio\themes\example\Models\Model as ArticleModel;

class Controller extends \packages\actionBasezio\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}