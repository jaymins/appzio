<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMvenues\themes\venuedefault\Controllers;

use packages\actionMvenues\themes\venuedefault\Views\View as ArticleView;
use packages\actionMvenues\themes\venuedefault\Models\Model as ArticleModel;

class Controller extends \packages\actionMvenues\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}