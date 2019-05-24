<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfitness\themes\uikit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMfitness\themes\uikit\Models\Model as ArticleModel;
use packages\actionMfitness\themes\uikit\Views\View as ArticleView;

class Controller extends BootstrapController
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

}