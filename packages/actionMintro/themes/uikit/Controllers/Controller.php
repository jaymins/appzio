<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMintro\themes\uikit\Controllers;

use packages\actionMintro\themes\uikit\Views\View as ArticleView;
use packages\actionMintro\themes\uikit\Models\Model as ArticleModel;

class Controller extends \packages\actionMintro\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}
