<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMcalendar\themes\test\Controllers;

use packages\actionMcalendar\themes\test\Views\View as ArticleView;
use packages\actionMcalendar\themes\test\Models\Model as ArticleModel;

class Controller extends \packages\actionMcalendar\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}