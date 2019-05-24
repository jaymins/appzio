<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\themes\uikit\Controllers;

use packages\actionMnexudus\themes\uikit\Views\View as ArticleView;
use packages\actionMnexudus\themes\uikit\Models\Model as ArticleModel;

class Controller extends \packages\actionMnexudus\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}
